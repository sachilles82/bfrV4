<?php

namespace Tests\Feature\Alem\User;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserPermanentDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Fixiere das Datum für die Tests, um konsistente Ergebnisse zu erhalten
        Carbon::setTestNow('2023-01-15 12:00:00');
    }

    protected function tearDown(): void
    {
        // Das Test-Datum zurücksetzen
        Carbon::setTestNow();
        parent::tearDown();
    }

    #[Test]
    public function prunable_only_returns_users_deleted_after_specified_days()
    {
        // Erstelle drei Benutzer mit unterschiedlichen Löschdaten
        $userDeletedRecently = User::factory()->create();
        $userDeletedRecently->deleted_at = now()->subDays(2);
        $userDeletedRecently->save();

        $userDeletedJustInTime = User::factory()->create();
        $userDeletedJustInTime->deleted_at = now()->subDays(User::getPermanentDeleteDays());
        $userDeletedJustInTime->save();

        $userDeletedLongAgo = User::factory()->create();
        $userDeletedLongAgo->deleted_at = now()->subDays(User::getPermanentDeleteDays() + 1);
        $userDeletedLongAgo->save();

        // Erstelle eine Instanz für den Zugriff auf die prunable-Methode
        $user = new User();
        $prunableQuery = $user->prunable();

        // Prüfen, ob der Query korrekt ist
        $prunableUsers = $prunableQuery->get();

        $this->assertFalse($prunableUsers->contains($userDeletedRecently->id));
        $this->assertTrue($prunableUsers->contains($userDeletedJustInTime->id));
        $this->assertTrue($prunableUsers->contains($userDeletedLongAgo->id));
    }

    #[Test]
    public function get_permanent_delete_days_returns_correct_value()
    {
        $this->assertEquals(7, User::getPermanentDeleteDays());
    }

    #[Test]
    public function days_until_permanent_delete_returns_correct_value()
    {
        // Benutzer erstellen und in den Papierkorb legen
        $user = User::factory()->create();

        // 3 Tage vor dem Test-Datum löschen
        $user->deleted_at = now()->subDays(3);
        $user->save();

        // Es sollten noch 4 Tage übrig sein (7 - 3)
        $this->assertEquals(4, $user->days_until_permanent_delete);
    }

    #[Test]
    public function days_until_permanent_delete_returns_zero_for_overdue_deletions()
    {
        // Benutzer erstellen und in den Papierkorb legen
        $user = User::factory()->create();

        // 10 Tage vor dem Test-Datum löschen (mehr als die 7 Tage)
        $user->deleted_at = now()->subDays(10);
        $user->save();

        // Es sollten 0 Tage übrig sein, nicht negativ
        $this->assertEquals(0, $user->days_until_permanent_delete);
    }

    #[Test]
    public function days_until_permanent_delete_returns_null_for_non_trashed_users()
    {
        // Benutzer erstellen, aber nicht löschen
        $user = User::factory()->create();

        // Für nicht gelöschte Benutzer sollte null zurückgegeben werden
        $this->assertNull($user->days_until_permanent_delete);
    }

    #[Test]
    public function permanent_deletion_date_returns_correct_date()
    {
        // Benutzer erstellen und in den Papierkorb legen
        $user = User::factory()->create();
        $deleteDate = now()->copy();
        $user->deleted_at = $deleteDate;
        $user->save();

        // Das Löschdatum sollte 7 Tage nach dem deleted_at Datum sein
        $expectedDate = $deleteDate->copy()->addDays(User::getPermanentDeleteDays());
        $this->assertEquals($expectedDate->timestamp, $user->permanent_deletion_date->timestamp);
    }

    #[Test]
    public function permanent_deletion_date_returns_null_for_non_trashed_users()
    {
        // Benutzer erstellen, aber nicht löschen
        $user = User::factory()->create();

        // Für nicht gelöschte Benutzer sollte null zurückgegeben werden
        $this->assertNull($user->permanent_deletion_date);
    }

    #[Test]
    public function deletion_message_shows_correct_message_for_different_days()
    {
        // Mock für Übersetzungen
        $this->mock('alias:__', function ($mock) {
            $mock->shouldReceive('__')
                ->with('Will be deleted soon')
                ->andReturn('Will be deleted soon');

            $mock->shouldReceive('__')
                ->with('Will be deleted tomorrow')
                ->andReturn('Will be deleted tomorrow');

            $mock->shouldReceive('__')
                ->with('Will be deleted in :days days', ['days' => 4])
                ->andReturn('Will be deleted in 4 days');
        });

        // Fall 1: 0 Tage übrig (wird bald gelöscht)
        $userOverdue = User::factory()->create();
        $userOverdue->deleted_at = now()->subDays(10); // Mehr als die 7 Tage
        $userOverdue->save();
        $this->assertEquals('Will be deleted soon', $userOverdue->deletion_message);

        // Fall 2: 1 Tag übrig (wird morgen gelöscht)
        $userTomorrow = User::factory()->create();
        $userTomorrow->deleted_at = now()->subDays(6); // 7 - 6 = 1 Tag übrig
        $userTomorrow->save();
        $this->assertEquals('Will be deleted tomorrow', $userTomorrow->deletion_message);

        // Fall 3: Mehrere Tage übrig
        $userDays = User::factory()->create();
        $userDays->deleted_at = now()->subDays(3); // 7 - 3 = 4 Tage übrig
        $userDays->save();
        $this->assertEquals('Will be deleted in 4 days', $userDays->deletion_message);
    }

    #[Test]
    public function deletion_message_returns_null_for_non_trashed_users()
    {
        // Benutzer erstellen, aber nicht löschen
        $user = User::factory()->create();

        // Für nicht gelöschte Benutzer sollte null zurückgegeben werden
        $this->assertNull($user->deletion_message);
    }

    #[Test]
    public function permanent_deletion_date_for_humans_formats_correctly()
    {
        // Benutzer erstellen und in den Papierkorb legen
        $user = User::factory()->create();

        // Test für Datum im selben Jahr
        $user->deleted_at = now()->subDays(3);
        $user->save();
        $expectedDate = now()->addDays(4)->format('M d, g:i A'); // 7 - 3 = 4 Tage bis zur Löschung
        $this->assertEquals($expectedDate, $user->permanent_deletion_date_for_humans);

        // Test für Datum in einem anderen Jahr
        $nextYear = now()->addYear()->subDays(3);
        $user->deleted_at = $nextYear;
        $user->save();
        $expectedDate = $nextYear->addDays(7)->format('M d, Y, g:i A');
        $this->assertEquals($expectedDate, $user->permanent_deletion_date_for_humans);
    }

    #[Test]
    public function permanent_deletion_date_for_humans_returns_null_for_non_trashed_users()
    {
        // Benutzer erstellen, aber nicht löschen
        $user = User::factory()->create();

        // Für nicht gelöschte Benutzer sollte null zurückgegeben werden
        $this->assertNull($user->permanent_deletion_date_for_humans);
    }

    #[Test]
    public function deletion_urgency_class_returns_correct_classes()
    {
        // Fall 1: Dringend (0-1 Tage)
        $userUrgent = User::factory()->create();
        $userUrgent->deleted_at = now()->subDays(6); // 1 Tag übrig
        $userUrgent->save();
        $this->assertEquals('text-red-600 dark:text-red-400 font-medium', $userUrgent->deletion_urgency_class);

        // Fall 2: Warnung (2-3 Tage)
        $userWarning = User::factory()->create();
        $userWarning->deleted_at = now()->subDays(4); // 3 Tage übrig
        $userWarning->save();
        $this->assertEquals('text-amber-600 dark:text-amber-400', $userWarning->deletion_urgency_class);

        // Fall 3: Normal (4+ Tage)
        $userNormal = User::factory()->create();
        $userNormal->deleted_at = now()->subDays(2); // 5 Tage übrig
        $userNormal->save();
        $this->assertEquals('text-gray-600 dark:text-gray-400', $userNormal->deletion_urgency_class);
    }

    #[Test]
    public function deletion_urgency_class_returns_null_for_non_trashed_users()
    {
        // Benutzer erstellen, aber nicht löschen
        $user = User::factory()->create();

        // Für nicht gelöschte Benutzer sollte null zurückgegeben werden
        $this->assertNull($user->deletion_urgency_class);
    }
}
