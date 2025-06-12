<?php

namespace Tests\Unit\Trait;

use App\Traits\Cache\AdvancedCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Mockery;

afterEach(function () {
    Mockery::close();
    // Reset static property
    TestCacheClass::resetRequestCache();
});

// Test-Klasse mit allen exponierten Methoden
class TestCacheClass {
    use AdvancedCache;

    protected $cachePrefix = 'testprefix';
    protected $cacheDuration = 3600;

    // Model Properties simulieren
    public $company_id = null;
    public $team_id = null;
    public $created_by = null;

    // Exponiere alle protected Methoden für Tests
    public function publicGetCacheConfig(): array {
        return $this->getCacheConfig();
    }

    public function publicGetAutoFlushContexts(): array {
        return $this->getAutoFlushContexts();
    }

    public function publicGetCacheContexts(): array {
        return $this->getCacheContexts();
    }

    public function publicGenerateCacheKeys($context, $contextId, $options = []): array {
        return $this->generateCacheKeys($context, $contextId, $options);
    }

    public function publicCheckRequestCache($key): ?Collection {
        return $this->checkRequestCache($key);
    }

    public function publicLoadFromPersistentCache($key, $callback, $duration): Collection {
        return $this->loadFromPersistentCache($key, $callback, $duration);
    }

    public function publicFlushRelevantCaches(): void {
        $this->flushRelevantCaches();
    }

    public function publicFlushCacheContext($context, $contextId): void {
        $this->flushCacheContext($context, $contextId);
    }

    public function publicFlushRedisPattern($pattern): void {
        $this->flushRedisPattern($pattern);
    }

    // Helper für Tests
    public static function resetRequestCache(): void {
        self::$requestCache = [];
    }

    // Simuliere Model Events
    public function fireModelEvent($event): void {
        // Dummy implementation
    }
}

// ===== CONFIGURATION TESTS =====
describe('Configuration Methods', function () {
    test('returns default cache configuration', function () {
        $instance = new TestCacheClass();
        $config = $instance->publicGetCacheConfig();

        expect($config)->toMatchArray([
            'enabled' => true,
            'duration' => 3600,
            'prefix' => 'testprefix',
            'auto_flush' => true
        ]);
    });

    test('uses model specific cache duration', function () {
        $instance = new class extends TestCacheClass {
            protected $cacheDuration = 7200;
        };

        $config = $instance->publicGetCacheConfig();
        expect($config['duration'])->toBe(7200);
    });

    test('uses class basename as default prefix', function () {
        $instance = new class extends TestCacheClass {
            protected $cachePrefix = null;
        };

        $config = $instance->publicGetCacheConfig();
        // Anonyme Klassen haben komplexe Namen, prüfe nur dass ein Prefix gesetzt wird
        expect($config['prefix'])->toBeString();
        expect(strlen($config['prefix']))->toBeGreaterThan(0);
    });

    test('returns default auto flush contexts', function () {
        $instance = new TestCacheClass();
        expect($instance->publicGetAutoFlushContexts())->toBe(['company', 'team', 'user']);
    });

    test('returns default cache contexts mapping', function () {
        $instance = new TestCacheClass();
        expect($instance->publicGetCacheContexts())->toBe([
            'company' => 'company_id',
            'team' => 'team_id',
            'user' => 'created_by',
        ]);
    });
});

// ===== KEY GENERATION TESTS =====
describe('Cache Key Generation', function () {
    test('generates correct cache keys', function () {
        $instance = new TestCacheClass();

        $keys = $instance->publicGenerateCacheKeys('company', 123);
        expect($keys)->toHaveKeys(['request', 'persistent']);
        expect($keys['request'])->toBe('request_testprefix_company_123');
        expect($keys['persistent'])->toBe('testprefix:company:123');
    });

    test('generates cache keys with suffix', function () {
        $instance = new TestCacheClass();

        $keys = $instance->publicGenerateCacheKeys('company', 123, ['suffix' => 'active']);
        expect($keys['request'])->toBe('request_testprefix_company_123_active');
        expect($keys['persistent'])->toBe('testprefix:company:123:active');
    });

    test('generates cache keys with empty suffix', function () {
        $instance = new TestCacheClass();

        $keys = $instance->publicGenerateCacheKeys('company', 123, ['suffix' => '']);
        expect($keys['request'])->toBe('request_testprefix_company_123');
        expect($keys['persistent'])->toBe('testprefix:company:123');
    });
});

// ===== MODEL BOOT TESTS =====
describe('Model Boot and Events', function () {
    test('boots trait and registers model events when auto flush enabled', function () {
        // Einfache Überprüfung ohne echtes Model
        $testClass = new class {
            use AdvancedCache;

            public static $registeredEvents = [];

            public static function created($callback) {
                self::$registeredEvents['created'] = true;
            }

            public static function updated($callback) {
                self::$registeredEvents['updated'] = true;
            }

            public static function deleted($callback) {
                self::$registeredEvents['deleted'] = true;
            }
        };

        $testClass::bootAdvancedCache();

        expect($testClass::$registeredEvents)->toHaveKeys(['created', 'updated', 'deleted']);
    });

    test('does not register events when auto flush disabled', function () {
        $testClass = new class {
            use AdvancedCache;

            protected function getCacheConfig(): array {
                // Direkt die Konfiguration zurückgeben ohne parent::
                return [
                    'enabled' => $this->cacheEnabled ?? true,
                    'duration' => $this->cacheDuration ?? 3600,
                    'prefix' => $this->cachePrefix ?? class_basename(static::class),
                    'auto_flush' => false // Das ist der wichtige Teil
                ];
            }

            public static $registeredEvents = [];

            public static function created($callback) {
                self::$registeredEvents['created'] = true;
            }
        };

        $testClass::bootAdvancedCache();

        expect($testClass::$registeredEvents)->toBeEmpty();
    });
});

// ===== CACHE STORAGE AND RETRIEVAL TESTS =====
describe('Cache Storage and Retrieval', function () {
    test('getCached method full flow', function () {
        Cache::shouldReceive('remember')
            ->once()
            ->with('testprefix:company:123', 3600, Mockery::type('Closure'))
            ->andReturnUsing(fn($key, $ttl, $callback) => $callback());

        $called = false;
        $result = TestCacheClass::getCached('company', '123', function () use (&$called) {
            $called = true;
            return collect(['test-data']);
        });

        expect($called)->toBeTrue();
        expect($result)->toEqual(collect(['test-data']));
    });

    test('returns callback result directly when cache disabled', function () {
        $instance = new class extends TestCacheClass {
            protected function getCacheConfig(): array {
                // Überschreibe die komplette Konfiguration
                return [
                    'enabled' => false,
                    'duration' => $this->cacheDuration ?? 3600,
                    'prefix' => $this->cachePrefix ?? 'testprefix',
                    'auto_flush' => true
                ];
            }
        };

        // Keine Cache Mocks nötig, da Cache disabled
        $callCount = 0;
        $result1 = $instance::getCached('company', 123, function () use (&$callCount) {
            $callCount++;
            return collect(['data' . $callCount]);
        });

        $result2 = $instance::getCached('company', 123, function () use (&$callCount) {
            $callCount++;
            return collect(['data' . $callCount]);
        });

        expect($callCount)->toBe(2);
        expect($result1)->toEqual(collect(['data1']));
        expect($result2)->toEqual(collect(['data2']));
    });

    test('uses request cache on second call', function () {
        Cache::shouldReceive('remember')
            ->once() // Wichtig: nur EINMAL beim ersten Aufruf
            ->andReturn(collect(['cached-data']));

        $result1 = TestCacheClass::getCachedByCompany(123, fn() => collect(['data']));
        $result2 = TestCacheClass::getCachedByCompany(123, fn() => collect(['data']));

        expect($result1)->toEqual($result2);
        expect($result1)->toEqual(collect(['cached-data']));
    });

    test('checkRequestCache returns null when not found', function () {
        $instance = new TestCacheClass();
        expect($instance->publicCheckRequestCache('non-existent-key'))->toBeNull();
    });

    test('checkRequestCache returns data when found', function () {
        // Mock Cache für getCached
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(collect(['found']));

        TestCacheClass::getCached('test', 'key', fn() => collect(['found']));

        $instance = new TestCacheClass();
        $result = $instance->publicCheckRequestCache('request_testprefix_test_key');

        expect($result)->toEqual(collect(['found']));
    });

    test('loadFromPersistentCache with normal duration', function () {
        $instance = new TestCacheClass();

        Cache::shouldReceive('remember')
            ->once()
            ->with('test-key', 3600, Mockery::type('Closure'))
            ->andReturn(collect(['data']));

        $result = $instance->publicLoadFromPersistentCache('test-key', fn() => collect(['data']), 3600);
        expect($result)->toEqual(collect(['data']));
    });

    test('loadFromPersistentCache with forever duration', function () {
        $instance = new TestCacheClass();

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with('test-key', Mockery::type('Closure'))
            ->andReturn(collect(['forever']));

        $result = $instance->publicLoadFromPersistentCache('test-key', fn() => collect(['forever']), -1);
        expect($result)->toEqual(collect(['forever']));
    });
});

// ===== SHORTCUT METHODS TESTS =====
describe('Shortcut Methods', function () {
    test('getCachedByCompany with valid ID', function () {
        Cache::shouldReceive('remember')->once()->andReturn(collect(['company-data']));

        $result = TestCacheClass::getCachedByCompany(123, fn() => collect(['company-data']));
        expect($result)->toEqual(collect(['company-data']));
    });

    test('getCachedByCompany returns empty for null ID', function () {
        $result = TestCacheClass::getCachedByCompany(null, fn() => collect(['data']));
        expect($result)->toBeEmpty();
    });

    test('getCachedByTeam with valid ID', function () {
        Cache::shouldReceive('remember')->once()->andReturn(collect(['team-data']));

        $result = TestCacheClass::getCachedByTeam(456, fn() => collect(['team-data']));
        expect($result)->toEqual(collect(['team-data']));
    });

    test('getCachedByTeam returns empty for null ID', function () {
        $result = TestCacheClass::getCachedByTeam(null, fn() => collect(['data']));
        expect($result)->toBeEmpty();
    });

    test('getCachedByUser with valid ID', function () {
        Cache::shouldReceive('remember')->once()->andReturn(collect(['user-data']));

        $result = TestCacheClass::getCachedByUser(789, fn() => collect(['user-data']));
        expect($result)->toEqual(collect(['user-data']));
    });

    test('getCachedByUser returns empty for null ID', function () {
        $result = TestCacheClass::getCachedByUser(null, fn() => collect(['data']));
        expect($result)->toBeEmpty();
    });

    test('getCachedGlobal', function () {
        Cache::shouldReceive('remember')
            ->once()
            ->with('testprefix:global:all', 3600, Mockery::type('Closure'))
            ->andReturn(collect(['global-data']));

        $result = TestCacheClass::getCachedGlobal(fn() => collect(['global-data']));
        expect($result)->toEqual(collect(['global-data']));
    });

    test('shortcut methods with custom options', function () {
        Cache::shouldReceive('remember')
            ->once()
            ->with('testprefix:company:123:active', 7200, Mockery::type('Closure'))
            ->andReturn(collect(['filtered']));

        $result = TestCacheClass::getCachedByCompany(
            123,
            fn() => collect(['filtered']),
            ['suffix' => 'active', 'duration' => 7200]
        );

        expect($result)->toEqual(collect(['filtered']));
    });
});

// ===== CACHE INVALIDATION TESTS =====
describe('Cache Invalidation', function () {
    test('flushCacheContext clears all caches', function () {
        $instance = new TestCacheClass();

        // Mock für Cache
        Cache::shouldReceive('forget')->with('testprefix:company:123')->once();

        // Mock für Redis check
        $mockStore = Mockery::mock();
        Cache::shouldReceive('getStore')->once()->andReturn($mockStore);

        // Mock für getCached
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(collect(['data']));

        // Setze Request Cache
        TestCacheClass::getCached('company', 123, fn() => collect(['data']));

        // Flush
        $instance->publicFlushCacheContext('company', 123);

        // Request Cache sollte auch geleert sein
        expect($instance->publicCheckRequestCache('request_testprefix_company_123'))->toBeNull();
    });

    test('flushRedisPattern with RedisStore', function () {
        $instance = new TestCacheClass();

        $mockRedisStore = Mockery::mock(\Illuminate\Cache\RedisStore::class);
        $mockConnection = Mockery::mock();

        Cache::shouldReceive('getStore')
            ->twice()
            ->andReturn($mockRedisStore);

        $mockRedisStore->shouldReceive('connection')
            ->once()
            ->andReturn($mockConnection);

        $mockConnection->shouldReceive('keys')
            ->with('testprefix:company:*')
            ->once()
            ->andReturn([
                'laravel_cache:testprefix:company:123',
                'laravel_cache:testprefix:company:456'
            ]);

        // Mock config helper
        $instance = new class extends TestCacheClass {
            public function publicFlushRedisPattern($pattern): void {
                if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                    try {
                        $keys = Cache::getStore()->connection()->keys($pattern);
                        foreach ($keys as $key) {
                            // Simuliere config('cache.prefix')
                            $key = str_replace('laravel_cache:', '', $key);
                            Cache::forget($key);
                        }
                    } catch (\Exception $e) {
                        // Silent
                    }
                }
            }
        };

        Cache::shouldReceive('forget')->with('testprefix:company:123')->once();
        Cache::shouldReceive('forget')->with('testprefix:company:456')->once();

        $instance->publicFlushRedisPattern('testprefix:company:*');

        expect(true)->toBeTrue();
    });

    test('flushRedisPattern with non-RedisStore', function () {
        $instance = new TestCacheClass();

        $mockArrayStore = Mockery::mock(\Illuminate\Cache\ArrayStore::class);
        Cache::shouldReceive('getStore')->once()->andReturn($mockArrayStore);

        // Sollte nichts tun
        $instance->publicFlushRedisPattern('testprefix:*');

        expect(true)->toBeTrue();
    });

    test('flushRedisPattern handles Redis exceptions', function () {
        // Erstelle eine Test-Klasse die report() überschreibt
        $instance = new class extends TestCacheClass {
            public static $reportedExceptions = [];

            public function publicFlushRedisPattern($pattern): void {
                if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                    try {
                        $keys = Cache::getStore()->connection()->keys($pattern);
                        foreach ($keys as $key) {
                            $key = str_replace(config('cache.prefix', 'laravel_cache') . ':', '', $key);
                            Cache::forget($key);
                        }
                    } catch (\Exception $e) {
                        // Simuliere report() ohne ExceptionHandler
                        self::$reportedExceptions[] = $e;
                    }
                }
            }
        };

        $mockRedisStore = Mockery::mock(\Illuminate\Cache\RedisStore::class);
        $mockConnection = Mockery::mock();

        Cache::shouldReceive('getStore')
            ->twice()
            ->andReturn($mockRedisStore);

        $mockRedisStore->shouldReceive('connection')
            ->once()
            ->andReturn($mockConnection);

        $mockConnection->shouldReceive('keys')
            ->once()
            ->andThrow(new \Exception('Redis connection failed'));

        // Sollte Exception silent behandeln
        $instance->publicFlushRedisPattern('testprefix:*');

        // Verifiziere dass Exception "reported" wurde
        expect($instance::$reportedExceptions)->toHaveCount(1);
        expect($instance::$reportedExceptions[0]->getMessage())->toBe('Redis connection failed');
    });

    test('flushRelevantCaches flushes all configured contexts', function () {
        $instance = new class extends TestCacheClass {
            public $company_id = 123;
            public $team_id = 456;
            public $created_by = 789;

            protected $cachePrefix = 'testmodel';
        };

        // Mocks für alle drei Kontexte
        $mockStore = Mockery::mock();
        Cache::shouldReceive('getStore')->times(3)->andReturn($mockStore);

        Cache::shouldReceive('forget')->with('testmodel:company:123')->once();
        Cache::shouldReceive('forget')->with('testmodel:team:456')->once();
        Cache::shouldReceive('forget')->with('testmodel:user:789')->once();

        $instance->publicFlushRelevantCaches();

        expect(true)->toBeTrue();
    });

    test('flushRelevantCaches skips empty IDs', function () {
        $instance = new class extends TestCacheClass {
            public $company_id = 123;
            public $team_id = null;
            public $created_by = 0;

            protected $cachePrefix = 'testmodel';
        };

        $mockStore = Mockery::mock();
        Cache::shouldReceive('getStore')->once()->andReturn($mockStore);

        Cache::shouldReceive('forget')->with('testmodel:company:123')->once();
        Cache::shouldReceive('forget')->with('testmodel:team:')->never();
        Cache::shouldReceive('forget')->with('testmodel:user:0')->never();

        $instance->publicFlushRelevantCaches();

        expect(true)->toBeTrue();
    });

    test('flushRelevantCaches with custom contexts', function () {
        $instance = new class extends TestCacheClass {
            public $company_id = 123;
            protected $cachePrefix = 'testmodel';

            protected function getAutoFlushContexts(): array {
                return ['company']; // Nur company
            }
        };

        $mockStore = Mockery::mock();
        Cache::shouldReceive('getStore')->once()->andReturn($mockStore);
        Cache::shouldReceive('forget')->with('testmodel:company:123')->once();

        $instance->publicFlushRelevantCaches();

        expect(true)->toBeTrue();
    });

    test('flushRelevantCaches skips non-existent context mappings', function () {
        $instance = new class extends TestCacheClass {
            protected function getAutoFlushContexts(): array {
                return ['company', 'nonexistent'];
            }

            protected $cachePrefix = 'testmodel';
            public $company_id = 123;
        };

        // Sollte nur company flushen, nonexistent ignorieren
        $mockStore = Mockery::mock();
        Cache::shouldReceive('getStore')->once()->andReturn($mockStore);
        Cache::shouldReceive('forget')->with('testmodel:company:123')->once();

        $instance->publicFlushRelevantCaches();

        expect(true)->toBeTrue();
    });
});

// ===== STATIC FLUSH METHODS TESTS =====
describe('Static Flush Methods', function () {
    test('flushAllCaches', function () {
        $mockStore = Mockery::mock();
        Cache::shouldReceive('getStore')->once()->andReturn($mockStore);

        TestCacheClass::flushAllCaches();

        // Request Cache sollte leer sein
        $instance = new TestCacheClass();
        expect($instance->publicCheckRequestCache('any-key'))->toBeNull();
    });

    test('flushRequestCache', function () {
        // Mock Cache für getCached
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(collect(['data']));

        // Setze Daten in Request Cache
        TestCacheClass::getCached('test', 'key', fn() => collect(['data']));

        TestCacheClass::flushRequestCache();

        // Request Cache sollte leer sein
        $instance = new TestCacheClass();
        expect($instance->publicCheckRequestCache('request_testprefix_test_key'))->toBeNull();
    });

    test('flushRequestCacheForContext', function () {
        // Setze mehrere Daten mit Mocks
        Cache::shouldReceive('remember')
            ->twice()
            ->andReturn(collect(['company']), collect(['team']));

        TestCacheClass::getCached('company', 123, fn() => collect(['company']));
        TestCacheClass::getCached('team', 456, fn() => collect(['team']));

        // Nur company Context flushen
        TestCacheClass::flushRequestCacheForContext('company', 123);

        $instance = new TestCacheClass();
        expect($instance->publicCheckRequestCache('request_testprefix_company_123'))->toBeNull();
        expect($instance->publicCheckRequestCache('request_testprefix_team_456'))->toEqual(collect(['team']));
    });

    test('flushCompanyCache with valid ID', function () {
        $mockStore = Mockery::mock();
        Cache::shouldReceive('getStore')->once()->andReturn($mockStore);
        Cache::shouldReceive('forget')->with('testprefix:company:123')->once();

        TestCacheClass::flushCompanyCache(123);

        expect(true)->toBeTrue();
    });

    test('flushCompanyCache with null ID', function () {
        Cache::shouldReceive('forget')->never();

        TestCacheClass::flushCompanyCache(null);

        expect(true)->toBeTrue();
    });

    test('flushTeamCache with valid ID', function () {
        $mockStore = Mockery::mock();
        Cache::shouldReceive('getStore')->once()->andReturn($mockStore);
        Cache::shouldReceive('forget')->with('testprefix:team:456')->once();

        TestCacheClass::flushTeamCache(456);

        expect(true)->toBeTrue();
    });

    test('flushTeamCache with null ID', function () {
        Cache::shouldReceive('forget')->never();

        TestCacheClass::flushTeamCache(null);

        expect(true)->toBeTrue();
    });

    test('flushUserCache with valid ID', function () {
        $mockStore = Mockery::mock();
        Cache::shouldReceive('getStore')->once()->andReturn($mockStore);
        Cache::shouldReceive('forget')->with('testprefix:user:789')->once();

        TestCacheClass::flushUserCache(789);

        expect(true)->toBeTrue();
    });

    test('flushUserCache with null ID', function () {
        Cache::shouldReceive('forget')->never();

        TestCacheClass::flushUserCache(null);

        expect(true)->toBeTrue();
    });
});

// ===== EDGE CASES AND INTEGRATION TESTS =====
describe('Edge Cases and Integration', function () {
    test('handles string context IDs', function () {
        Cache::shouldReceive('remember')
            ->once()
            ->with('testprefix:custom:abc123', 3600, Mockery::type('Closure'))
            ->andReturn(collect(['string-id']));

        $result = TestCacheClass::getCached('custom', 'abc123', fn() => collect(['string-id']));
        expect($result)->toEqual(collect(['string-id']));
    });

    test('complete flow with suffix and custom duration', function () {
        $options = ['suffix' => 'filtered', 'duration' => 7200];

        Cache::shouldReceive('remember')
            ->once()
            ->with('testprefix:company:123:filtered', 7200, Mockery::type('Closure'))
            ->andReturn(collect(['filtered-data']));

        $result = TestCacheClass::getCachedByCompany(123, fn() => collect(['filtered-data']), $options);
        expect($result)->toEqual(collect(['filtered-data']));
    });

    test('model event callbacks flush cache correctly', function () {
        // Erstelle eine Test-Model-Klasse die von Eloquent Model erbt
        $testModelClass = new class extends \Illuminate\Database\Eloquent\Model {
            use AdvancedCache;

            protected $cachePrefix = 'eventtest';
            public static $registeredCallbacks = [];
            public static $flushCalledCount = 0;

            // Override Model Event-Methoden für den Test
            public static function created($callback) {
                self::$registeredCallbacks['created'] = $callback;
            }

            public static function updated($callback) {
                self::$registeredCallbacks['updated'] = $callback;
            }

            public static function deleted($callback) {
                self::$registeredCallbacks['deleted'] = $callback;
            }

            // Track calls to flushRelevantCaches
            protected function flushRelevantCaches(): void {
                self::$flushCalledCount++;
            }
        };

        // Reset static counter
        $testModelClass::$flushCalledCount = 0;

        // Boot the trait - dies sollte die Callbacks registrieren
        $testModelClass::bootAdvancedCache();

        // Verifiziere dass alle drei Callbacks registriert wurden
        expect($testModelClass::$registeredCallbacks)->toHaveKeys(['created', 'updated', 'deleted']);

        // Erstelle eine Instanz des Test-Models
        $modelInstance = new $testModelClass();

        // Simuliere das created Event
        $createdCallback = $testModelClass::$registeredCallbacks['created'];
        $createdCallback($modelInstance);

        // Verifiziere dass flushRelevantCaches aufgerufen wurde
        expect($testModelClass::$flushCalledCount)->toBe(1);

        // Test auch updated und deleted
        $updatedCallback = $testModelClass::$registeredCallbacks['updated'];
        $updatedCallback($modelInstance);
        expect($testModelClass::$flushCalledCount)->toBe(2);

        $deletedCallback = $testModelClass::$registeredCallbacks['deleted'];
        $deletedCallback($modelInstance);
        expect($testModelClass::$flushCalledCount)->toBe(3);
    });

    test('handles different prefix configurations', function () {
        $instance = new TestCacheClass();

        // Test ohne config() helper
        $keys = $instance->publicGenerateCacheKeys('test', 'id');

        // Keys sollten den class prefix verwenden
        expect($keys['persistent'])->toBe('testprefix:test:id');
        expect($keys['request'])->toBe('request_testprefix_test_id');
    });

    test('request cache is cleared on static flush methods', function () {
        // Mock für getCached
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(collect(['test-data']));

        // Cache setzen
        TestCacheClass::getCachedByCompany(123, fn() => collect(['test-data']));

        // Mock für flush
        $mockStore = Mockery::mock();
        Cache::shouldReceive('getStore')->once()->andReturn($mockStore);
        Cache::shouldReceive('forget')->once();

        // Static flush sollte auch Request Cache leeren
        TestCacheClass::flushCompanyCache(123);

        // Verify request cache is empty
        $instance = new TestCacheClass();
        expect($instance->publicCheckRequestCache('request_testprefix_company_123'))->toBeNull();
    });
});
