<?php

namespace Tests\Feature\Trait;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Nur RefreshDatabase, da TestCase bereits in Pest.php definiert ist
uses(RefreshDatabase::class);

beforeEach(function () {
    // Cache vor jedem Test leeren
    Cache::flush();

    // Test-Tabelle erstellen
    Schema::create('test_cache_models', function (Blueprint $table) {
        $table->id();
        $table->string('name')->nullable();
        $table->unsignedBigInteger('company_id')->nullable();
        $table->unsignedBigInteger('team_id')->nullable();
        $table->unsignedBigInteger('created_by')->nullable();
        $table->timestamps();
    });
});

afterEach(function () {
    // Tabelle nach jedem Test löschen
    Schema::dropIfExists('test_cache_models');
});

// Basis Test-Model Klasse für alle Tests
class TestCacheModel extends \Illuminate\Database\Eloquent\Model {
    use \App\Traits\Cache\AdvancedCache;

    protected $table = 'test_cache_models';
    protected $fillable = ['name', 'company_id', 'team_id', 'created_by'];
    protected $cachePrefix = 'testmodel';
}

test('trait can be used on model', function () {
    $model = new TestCacheModel();
    expect(in_array(\App\Traits\Cache\AdvancedCache::class, class_uses($model)))->toBeTrue();
});

test('caches data by company context', function () {
    $companyId = 123;
    $expectedData = collect(['item1', 'item2', 'item3']);
    $callCount = 0;

    $dataCallback = function () use ($expectedData, &$callCount) {
        $callCount++;
        return $expectedData;
    };

    // Erster Aufruf - sollte Callback ausführen
    $result1 = TestCacheModel::getCachedByCompany($companyId, $dataCallback);
    expect($result1)->toEqual($expectedData);
    expect($callCount)->toBe(1);

    // Zweiter Aufruf - sollte aus Cache kommen
    $result2 = TestCacheModel::getCachedByCompany($companyId, $dataCallback);
    expect($result2)->toEqual($expectedData);
    expect($callCount)->toBe(1); // Callback wurde nicht erneut aufgerufen
});

test('caches data by team context', function () {
    $teamId = 456;
    $expectedData = collect(['team1', 'team2']);
    $callCount = 0;

    $result = TestCacheModel::getCachedByTeam($teamId, function () use ($expectedData, &$callCount) {
        $callCount++;
        return $expectedData;
    });

    expect($result)->toEqual($expectedData);
    expect($callCount)->toBe(1);
    expect(Cache::has('testmodel:team:456'))->toBeTrue();
});

test('caches data by user context', function () {
    $userId = 789;
    $expectedData = collect(['user1', 'user2']);

    $result = TestCacheModel::getCachedByUser($userId, fn() => $expectedData);

    expect($result)->toEqual($expectedData);
    expect(Cache::has('testmodel:user:789'))->toBeTrue();
});

test('caches global data', function () {
    $expectedData = collect(['global1', 'global2']);

    $result = TestCacheModel::getCachedGlobal(fn() => $expectedData);

    expect($result)->toEqual($expectedData);
    expect(Cache::has('testmodel:global:all'))->toBeTrue();
});

test('returns empty collection for null context IDs', function () {
    expect(TestCacheModel::getCachedByCompany(null, fn() => collect(['data'])))->toBeEmpty();
    expect(TestCacheModel::getCachedByTeam(null, fn() => collect(['data'])))->toBeEmpty();
    expect(TestCacheModel::getCachedByUser(null, fn() => collect(['data'])))->toBeEmpty();
});

test('flushes company cache', function () {
    $companyId = 123;

    // Cache setzen
    TestCacheModel::getCachedByCompany($companyId, fn() => collect(['data']));
    expect(Cache::has('testmodel:company:123'))->toBeTrue();

    // Cache flushen
    TestCacheModel::flushCompanyCache($companyId);

    expect(Cache::has('testmodel:company:123'))->toBeFalse();
});

test('flushes team cache', function () {
    $teamId = 456;

    TestCacheModel::getCachedByTeam($teamId, fn() => collect(['data']));
    TestCacheModel::flushTeamCache($teamId);

    expect(Cache::has('testmodel:team:456'))->toBeFalse();
});

test('flushes user cache', function () {
    $userId = 789;

    TestCacheModel::getCachedByUser($userId, fn() => collect(['data']));
    TestCacheModel::flushUserCache($userId);

    expect(Cache::has('testmodel:user:789'))->toBeFalse();
});

test('flushes all caches for model', function () {
    // Mehrere Caches setzen
    TestCacheModel::getCachedByCompany(123, fn() => collect(['company']));
    TestCacheModel::getCachedByTeam(456, fn() => collect(['team']));
    TestCacheModel::getCachedGlobal(fn() => collect(['global']));

    // Alle Caches flushen
    TestCacheModel::flushAllCaches();

    expect(Cache::has('testmodel:company:123'))->toBeFalse();
    expect(Cache::has('testmodel:team:456'))->toBeFalse();
    expect(Cache::has('testmodel:global:all'))->toBeFalse();
});

test('automatically flushes cache on model save', function () {
    // Cache setzen
    TestCacheModel::getCachedByCompany(123, fn() => collect(['cached data']));
    expect(Cache::has('testmodel:company:123'))->toBeTrue();

    // Model erstellen - sollte Cache flushen
    $model = TestCacheModel::create(['name' => 'Test', 'company_id' => 123]);

    // Cache sollte geleert sein
    expect(Cache::has('testmodel:company:123'))->toBeFalse();
});

test('automatically flushes cache on model update', function () {
    $model = TestCacheModel::create(['name' => 'Test', 'company_id' => 123]);

    // Cache setzen
    TestCacheModel::getCachedByCompany(123, fn() => collect(['cached data']));
    expect(Cache::has('testmodel:company:123'))->toBeTrue();

    // Model updaten
    $model->update(['name' => 'Updated']);

    expect(Cache::has('testmodel:company:123'))->toBeFalse();
});

test('automatically flushes cache on model delete', function () {
    $model = TestCacheModel::create(['name' => 'Test', 'company_id' => 123]);

    // Cache setzen
    TestCacheModel::getCachedByCompany(123, fn() => collect(['cached data']));

    // Model löschen
    $model->delete();

    expect(Cache::has('testmodel:company:123'))->toBeFalse();
});

test('flushes multiple contexts when model has multiple IDs', function () {
    // Caches für verschiedene Kontexte setzen
    TestCacheModel::getCachedByCompany(123, fn() => collect(['company']));
    TestCacheModel::getCachedByTeam(456, fn() => collect(['team']));
    TestCacheModel::getCachedByUser(789, fn() => collect(['user']));

    // Model mit allen IDs erstellen
    $model = TestCacheModel::create([
        'name' => 'Test',
        'company_id' => 123,
        'team_id' => 456,
        'created_by' => 789
    ]);

    // Alle relevanten Caches sollten geflusht sein
    expect(Cache::has('testmodel:company:123'))->toBeFalse();
    expect(Cache::has('testmodel:team:456'))->toBeFalse();
    expect(Cache::has('testmodel:user:789'))->toBeFalse();
});

test('request cache prevents duplicate queries in same request', function () {
    $callCount = 0;
    $dataCallback = function () use (&$callCount) {
        $callCount++;
        return collect(['data']);
    };

    // Mehrfache Aufrufe im selben Request
    TestCacheModel::getCachedByCompany(123, $dataCallback);
    TestCacheModel::getCachedByCompany(123, $dataCallback);
    TestCacheModel::getCachedByCompany(123, $dataCallback);

    // Callback sollte nur einmal aufgerufen worden sein
    expect($callCount)->toBe(1);

    // Nach Request Cache flush
    TestCacheModel::flushRequestCache();

    // Nächster Aufruf sollte aus persistentem Cache kommen (nicht Callback)
    TestCacheModel::getCachedByCompany(123, $dataCallback);
    expect($callCount)->toBe(1);
});

test('handles cache with custom duration', function () {
    TestCacheModel::getCachedByCompany(
        123,
        fn() => collect(['custom duration']),
        ['duration' => 7200] // 2 Stunden
    );

    expect(Cache::has('testmodel:company:123'))->toBeTrue();
});

test('handles cache with suffix option', function () {
    TestCacheModel::getCachedByCompany(
        123,
        fn() => collect(['filtered']),
        ['suffix' => 'active']
    );

    expect(Cache::has('testmodel:company:123:active'))->toBeTrue();
});

test('handles cache suffix with multiple contexts', function () {
    // Company mit Suffix
    TestCacheModel::getCachedByCompany(123, fn() => collect(['active companies']), ['suffix' => 'active']);
    TestCacheModel::getCachedByCompany(123, fn() => collect(['inactive companies']), ['suffix' => 'inactive']);

    expect(Cache::has('testmodel:company:123:active'))->toBeTrue();
    expect(Cache::has('testmodel:company:123:inactive'))->toBeTrue();

    // Flush ohne Suffix sollte beide nicht betreffen
    TestCacheModel::flushCompanyCache(123);

    // Beide sollten noch existieren (da sie mit Suffix gespeichert wurden)
    expect(Cache::has('testmodel:company:123:active'))->toBeTrue();
    expect(Cache::has('testmodel:company:123:inactive'))->toBeTrue();
});

test('flushes request cache for specific context', function () {
    $companyId1 = 123;
    $companyId2 = 456;
    $callCount1 = 0;
    $callCount2 = 0;

    TestCacheModel::getCachedByCompany($companyId1, function () use (&$callCount1) {
        $callCount1++;
        return collect(['company1']);
    });

    TestCacheModel::getCachedByCompany($companyId2, function () use (&$callCount2) {
        $callCount2++;
        return collect(['company2']);
    });

    expect($callCount1)->toBe(1);
    expect($callCount2)->toBe(1);

    // Nur einen Context flushen
    TestCacheModel::flushRequestCacheForContext('company', $companyId1);

    // Company 2 sollte noch im Request Cache sein
    TestCacheModel::getCachedByCompany($companyId2, function () use (&$callCount2) {
        $callCount2++;
        return collect(['company2']);
    });

    expect($callCount2)->toBe(1); // Callback wurde nicht erneut aufgerufen
});

test('handles null values gracefully when flushing', function () {
    // Sollte keine Exceptions werfen
    TestCacheModel::flushCompanyCache(null);
    TestCacheModel::flushTeamCache(null);
    TestCacheModel::flushUserCache(null);

    expect(true)->toBeTrue();
});
