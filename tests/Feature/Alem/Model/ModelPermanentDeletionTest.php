<?php

uses(\Illuminate\Database\Eloquent\Model::class);
use Carbon\Carbon;
use \Illuminate\Database\Eloquent\Model;
use \Tests\Feature\Alem\Model\TestPermanentDeletionModel;
use PHPUnit\Framework\Attributes\Test;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Temporäre Modelldefinition für Tests
    createTestModelTable();

    // Fixiere das Datum für die Tests, um konsistente Ergebnisse zu erhalten
    Carbon::setTestNow('2023-01-15 12:00:00');

    // Erstelle eine Instanz des Testmodells
    $this->testModel = new TestPermanentDeletionModel;
});

afterEach(function () {
    // Das Test-Datum zurücksetzen
    Carbon::setTestNow();
});

/**
 * Erstellt eine temporäre Tabelle für Tests
 */
function createTestModelTable(): void
{
    \Schema::create('test_permanent_deletion_models', function ($table) {
        $table->id();
        $table->string('name')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}

it('returns correct days until permanent delete', function () {
    // Speichere das Modell und setze das Löschdatum
    $this->testModel->name = 'Test Model';
    $this->testModel->save();

    // 3 Tage vor dem Test-Datum löschen
    $this->testModel->deleted_at = now()->subDays(3);
    $this->testModel->save();

    // Es sollten noch 4 Tage übrig sein (7 - 3)
    expect($this->testModel->days_until_permanent_delete)->toEqual(4);
});

it('returns zero days for overdue deletions', function () {
    $this->testModel->name = 'Test Model';
    $this->testModel->save();

    // 10 Tage vor dem Test-Datum löschen (mehr als die 7 Tage)
    $this->testModel->deleted_at = now()->subDays(10);
    $this->testModel->save();

    // Es sollten 0 Tage übrig sein, nicht negativ
    expect($this->testModel->days_until_permanent_delete)->toEqual(0);
});

it('returns null for days until delete for non trashed models', function () {
    $this->testModel->name = 'Test Model';
    $this->testModel->save();

    // Für nicht gelöschte Modelle sollte null zurückgegeben werden
    expect($this->testModel->days_until_permanent_delete)->toBeNull();
});

it('returns correct permanent deletion date', function () {
    $this->testModel->name = 'Test Model';
    $this->testModel->save();

    // Löschdatum festlegen
    $deleteDate = now()->copy();
    $this->testModel->deleted_at = $deleteDate;
    $this->testModel->save();

    // Das Löschdatum sollte 7 Tage nach dem deleted_at Datum sein
    $expectedDate = $deleteDate->copy()->addDays(TestPermanentDeletionModel::getPermanentDeleteDays());
    expect($this->testModel->permanent_deletion_date->timestamp)->toEqual($expectedDate->timestamp);
});

it('returns null permanent deletion date for non trashed models', function () {
    $this->testModel->name = 'Test Model';
    $this->testModel->save();

    // Für nicht gelöschte Modelle sollte null zurückgegeben werden
    expect($this->testModel->permanent_deletion_date)->toBeNull();
});

it('returns correct deletion message', function () {
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

    $this->testModel->deleted_at = now()->subDays(10);
    // Mehr als die 7 Tage
    $this->testModel->save();

    expect($this->testModel->deletion_message)->toEqual('Will be deleted soon');

    // Fall 2: 1 Tag übrig (wird morgen gelöscht)
    $translator->shouldReceive('get')
        ->with('Will be deleted tomorrow', [], null)
        ->andReturn('Will be deleted tomorrow');

    $this->testModel->deleted_at = now()->subDays(6);
    // 7 - 6 = 1 Tag übrig
    $this->testModel->save();

    // Hier muss __() in der Trait-Methode verwendet werden statt **()
    // Diese Assertion wird fehlschlagen, wenn der Trait-Code nicht korrigiert wurde
    expect($this->testModel->deletion_message)->toEqual('Will be deleted tomorrow');

    // Fall 3: Mehrere Tage übrig
    $translator->shouldReceive('get')
        ->with('Will be deleted in :days days', ['days' => 4], null)
        ->andReturn('Will be deleted in 4 days');

    $this->testModel->deleted_at = now()->subDays(3);
    // 7 - 3 = 4 Tage übrig
    $this->testModel->save();

    // Hier muss __() in der Trait-Methode verwendet werden statt **()
    // Diese Assertion wird fehlschlagen, wenn der Trait-Code nicht korrigiert wurde
    expect($this->testModel->deletion_message)->toEqual('Will be deleted in 4 days');
});

it('returns null deletion message for non trashed models', function () {
    $this->testModel->name = 'Test Model';
    $this->testModel->save();

    // Für nicht gelöschte Modelle sollte null zurückgegeben werden
    expect($this->testModel->deletion_message)->toBeNull();
});

it('formats permanent deletion date for humans correctly', function () {
    $this->testModel->name = 'Test Model';
    $this->testModel->save();

    // Test für Datum im selben Jahr
    $this->testModel->deleted_at = now()->subDays(3);
    $this->testModel->save();
    $expectedDate = now()->addDays(4)->format('M d, g:i A');
    // 7 - 3 = 4 Tage bis zur Löschung
    expect($this->testModel->permanent_deletion_date_for_humans)->toEqual($expectedDate);

    // Test für Datum in einem anderen Jahr
    $nextYear = now()->addYear()->subDays(3);
    $this->testModel->deleted_at = $nextYear;
    $this->testModel->save();
    $expectedDate = $nextYear->addDays(7)->format('M d, Y, g:i A');
    expect($this->testModel->permanent_deletion_date_for_humans)->toEqual($expectedDate);
});

it('returns null permanent deletion date for humans for non trashed models', function () {
    $this->testModel->name = 'Test Model';
    $this->testModel->save();

    // Für nicht gelöschte Modelle sollte null zurückgegeben werden
    expect($this->testModel->permanent_deletion_date_for_humans)->toBeNull();
});

it('returns correct deletion urgency class', function () {
    $this->testModel->name = 'Test Model';
    $this->testModel->save();

    // Fall 1: Dringend (0-1 Tage)
    $this->testModel->deleted_at = now()->subDays(6);
    // 1 Tag übrig
    $this->testModel->save();
    expect($this->testModel->deletion_urgency_class)->toEqual('text-red-600 dark:text-red-400 font-medium');

    // Fall 2: Warnung (2-3 Tage)
    $this->testModel->deleted_at = now()->subDays(4);
    // 3 Tage übrig
    $this->testModel->save();
    expect($this->testModel->deletion_urgency_class)->toEqual('text-amber-600 dark:text-amber-400');

    // Fall 3: Normal (4+ Tage)
    $this->testModel->deleted_at = now()->subDays(2);
    // 5 Tage übrig
    $this->testModel->save();
    expect($this->testModel->deletion_urgency_class)->toEqual('text-gray-600 dark:text-gray-400');
});

it('returns null deletion urgency class for non trashed models', function () {
    $this->testModel->name = 'Test Model';
    $this->testModel->save();

    // Für nicht gelöschte Modelle sollte null zurückgegeben werden
    expect($this->testModel->deletion_urgency_class)->toBeNull();
});

test('prunable query only returns models deleted after specified days', function () {
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

    expect($prunableModels->contains($modelDeletedRecently->id))->toBeFalse();
    expect($prunableModels->contains($modelDeletedJustInTime->id))->toBeTrue();
    expect($prunableModels->contains($modelDeletedLongAgo->id))->toBeTrue();
});

test('get permanent delete days returns correct value', function () {
    expect(TestPermanentDeletionModel::getPermanentDeleteDays())->toEqual(7);
});

/**
 * Testmodell zur Verwendung in den Tests
 */
class TestPermanentDeletionModel extends Model
{
    uses(\App\Traits\Model\ModelPermanentDeletion::class);
    uses(\Illuminate\Database\Eloquent\SoftDeletes::class);
    
}