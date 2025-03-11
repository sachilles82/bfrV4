<?php

namespace Tests\Feature\Alem\Model;

use App\Traits\Model\ModelPermanentDeletion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test für das ModelPermanentDeletion Trait.
 *
 * Wir erstellen ein temporäres Testmodell, das das Trait verwendet,
 * um es unabhängig von konkreten Modellen zu testen.
 */
class ModelPermanentDeletionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Temporäre Testmodell-Klasse, die das zu testende Trait verwendet
     */
    protected $testModel;

    protected function setUp(): void
    {
        parent::setUp();

        // Temporäre Modelldefinition für Tests
        $this->createTestModelTable();

        // Fixiere das Datum für die Tests, um konsistente Ergebnisse zu erhalten
        Carbon::setTestNow('2023-01-15 12:00:00');

        // Erstelle eine Instanz des Testmodells
        $this->testModel = new TestPermanentDeletionModel();
    }

    protected function tearDown(): void
    {
        // Das Test-Datum zurücksetzen
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Erstellt eine temporäre Tabelle für Tests
     */
    private function createTestModelTable(): void
    {
        \Schema::create('test_permanent_deletion_models', function ($table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    #[Test]
    public function it_returns_correct_days_until_permanent_delete()
    {
        // Speichere das Modell und setze das Löschdatum
        $this->testModel->name = 'Test Model';
        $this->testModel->save();

        // 3 Tage vor dem Test-Datum löschen
        $this->testModel->deleted_at = now()->subDays(3);
        $this->testModel->save();

        // Es sollten noch 4 Tage übrig sein (7 - 3)
        $this->assertEquals(4, $this->testModel->days_until_permanent_delete);
    }

    #[Test]
    public function it_returns_zero_days_for_overdue_deletions()
    {
        $this->testModel->name = 'Test Model';
        $this->testModel->save();

        // 10 Tage vor dem Test-Datum löschen (mehr als die 7 Tage)
        $this->testModel->deleted_at = now()->subDays(10);
        $this->testModel->save();

        // Es sollten 0 Tage übrig sein, nicht negativ
        $this->assertEquals(0, $this->testModel->days_until_permanent_delete);
    }

    #[Test]
    public function it_returns_null_for_days_until_delete_for_non_trashed_models()
    {
        $this->testModel->name = 'Test Model';
        $this->testModel->save();

        // Für nicht gelöschte Modelle sollte null zurückgegeben werden
        $this->assertNull($this->testModel->days_until_permanent_delete);
    }

    #[Test]
    public function it_returns_correct_permanent_deletion_date()
    {
        $this->testModel->name = 'Test Model';
        $this->testModel->save();

        // Löschdatum festlegen
        $deleteDate = now()->copy();
        $this->testModel->deleted_at = $deleteDate;
        $this->testModel->save();

        // Das Löschdatum sollte 7 Tage nach dem deleted_at Datum sein
        $expectedDate = $deleteDate->copy()->addDays(TestPermanentDeletionModel::getPermanentDeleteDays());
        $this->assertEquals(
            $expectedDate->timestamp,
            $this->testModel->permanent_deletion_date->timestamp
        );
    }

    #[Test]
    public function it_returns_null_permanent_deletion_date_for_non_trashed_models()
    {
        $this->testModel->name = 'Test Model';
        $this->testModel->save();

        // Für nicht gelöschte Modelle sollte null zurückgegeben werden
        $this->assertNull($this->testModel->permanent_deletion_date);
    }

    #[Test]
    public function it_returns_correct_deletion_message()
    {
        // Teste die verschiedenen Nachrichtentypen mit Mocks für die Übersetzungsfunktionen

        // Erstelle das Modell
        $this->testModel->name = 'Test Model';
        $this->testModel->save();

        // Mock für Übersetzungen
        app()->instance('translator', $translator = \Mockery::mock('translator'));

        // Fall 1: 0 Tage übrig (wird bald gelöscht)
        $translator->shouldReceive('get')
            ->with('Will be deleted soon', [], null)
            ->andReturn('Will be deleted soon');

        $this->testModel->deleted_at = now()->subDays(10); // Mehr als die 7 Tage
        $this->testModel->save();

        $this->assertEquals('Will be deleted soon', $this->testModel->deletion_message);

        // Fall 2: 1 Tag übrig (wird morgen gelöscht)
        $translator->shouldReceive('get')
            ->with('Will be deleted tomorrow', [], null)
            ->andReturn('Will be deleted tomorrow');

        $this->testModel->deleted_at = now()->subDays(6); // 7 - 6 = 1 Tag übrig
        $this->testModel->save();

        // Hier muss __() in der Trait-Methode verwendet werden statt **()
        // Diese Assertion wird fehlschlagen, wenn der Trait-Code nicht korrigiert wurde
        $this->assertEquals('Will be deleted tomorrow', $this->testModel->deletion_message);

        // Fall 3: Mehrere Tage übrig
        $translator->shouldReceive('get')
            ->with('Will be deleted in :days days', ['days' => 4], null)
            ->andReturn('Will be deleted in 4 days');

        $this->testModel->deleted_at = now()->subDays(3); // 7 - 3 = 4 Tage übrig
        $this->testModel->save();

        // Hier muss __() in der Trait-Methode verwendet werden statt **()
        // Diese Assertion wird fehlschlagen, wenn der Trait-Code nicht korrigiert wurde
        $this->assertEquals('Will be deleted in 4 days', $this->testModel->deletion_message);
    }

    #[Test]
    public function it_returns_null_deletion_message_for_non_trashed_models()
    {
        $this->testModel->name = 'Test Model';
        $this->testModel->save();

        // Für nicht gelöschte Modelle sollte null zurückgegeben werden
        $this->assertNull($this->testModel->deletion_message);
    }

    #[Test]
    public function it_formats_permanent_deletion_date_for_humans_correctly()
    {
        $this->testModel->name = 'Test Model';
        $this->testModel->save();

        // Test für Datum im selben Jahr
        $this->testModel->deleted_at = now()->subDays(3);
        $this->testModel->save();
        $expectedDate = now()->addDays(4)->format('M d, g:i A'); // 7 - 3 = 4 Tage bis zur Löschung
        $this->assertEquals($expectedDate, $this->testModel->permanent_deletion_date_for_humans);

        // Test für Datum in einem anderen Jahr
        $nextYear = now()->addYear()->subDays(3);
        $this->testModel->deleted_at = $nextYear;
        $this->testModel->save();
        $expectedDate = $nextYear->addDays(7)->format('M d, Y, g:i A');
        $this->assertEquals($expectedDate, $this->testModel->permanent_deletion_date_for_humans);
    }

    #[Test]
    public function it_returns_null_permanent_deletion_date_for_humans_for_non_trashed_models()
    {
        $this->testModel->name = 'Test Model';
        $this->testModel->save();

        // Für nicht gelöschte Modelle sollte null zurückgegeben werden
        $this->assertNull($this->testModel->permanent_deletion_date_for_humans);
    }

    #[Test]
    public function it_returns_correct_deletion_urgency_class()
    {
        $this->testModel->name = 'Test Model';
        $this->testModel->save();

        // Fall 1: Dringend (0-1 Tage)
        $this->testModel->deleted_at = now()->subDays(6); // 1 Tag übrig
        $this->testModel->save();
        $this->assertEquals('text-red-600 dark:text-red-400 font-medium', $this->testModel->deletion_urgency_class);

        // Fall 2: Warnung (2-3 Tage)
        $this->testModel->deleted_at = now()->subDays(4); // 3 Tage übrig
        $this->testModel->save();
        $this->assertEquals('text-amber-600 dark:text-amber-400', $this->testModel->deletion_urgency_class);

        // Fall 3: Normal (4+ Tage)
        $this->testModel->deleted_at = now()->subDays(2); // 5 Tage übrig
        $this->testModel->save();
        $this->assertEquals('text-gray-600 dark:text-gray-400', $this->testModel->deletion_urgency_class);
    }

    #[Test]
    public function it_returns_null_deletion_urgency_class_for_non_trashed_models()
    {
        $this->testModel->name = 'Test Model';
        $this->testModel->save();

        // Für nicht gelöschte Modelle sollte null zurückgegeben werden
        $this->assertNull($this->testModel->deletion_urgency_class);
    }

    #[Test]
    public function prunable_query_only_returns_models_deleted_after_specified_days()
    {
        // Erstelle drei Modelle mit unterschiedlichen Löschdaten
        $modelDeletedRecently = new TestPermanentDeletionModel(['name' => 'Recent']);
        $modelDeletedRecently->save();
        $modelDeletedRecently->deleted_at = now()->subDays(2);
        $modelDeletedRecently->save();

        $modelDeletedJustInTime = new TestPermanentDeletionModel(['name' => 'Just in time']);
        $modelDeletedJustInTime->save();
        $modelDeletedJustInTime->deleted_at = now()->subDays(TestPermanentDeletionModel::getPermanentDeleteDays());
        $modelDeletedJustInTime->save();

        $modelDeletedLongAgo = new TestPermanentDeletionModel(['name' => 'Long ago']);
        $modelDeletedLongAgo->save();
        $modelDeletedLongAgo->deleted_at = now()->subDays(TestPermanentDeletionModel::getPermanentDeleteDays() + 1);
        $modelDeletedLongAgo->save();

        // Überprüfe den prunable Query
        $prunableQuery = $this->testModel->prunable();
        $prunableModels = $prunableQuery->get();

        $this->assertFalse($prunableModels->contains($modelDeletedRecently->id));
        $this->assertTrue($prunableModels->contains($modelDeletedJustInTime->id));
        $this->assertTrue($prunableModels->contains($modelDeletedLongAgo->id));
    }

    #[Test]
    public function get_permanent_delete_days_returns_correct_value()
    {
        $this->assertEquals(7, TestPermanentDeletionModel::getPermanentDeleteDays());
    }
}

/**
 * Testmodell zur Verwendung in den Tests
 */
class TestPermanentDeletionModel extends Model
{
    use SoftDeletes, ModelPermanentDeletion;

    protected $table = 'test_permanent_deletion_models';
    protected $fillable = ['name'];

    // Stelle sicher, dass deleted_at als Datum behandelt wird
    protected $dates = ['deleted_at'];
}
