<?php

namespace Luxury\Support\Facades;

use Luxury\Constants\Services;

/**
 * Class Cache Facade of Luxury\Cache\Strategy
 *
 * @package Luxury\Support\Facades
 *
 * @method static \Phalcon\Cache\BackendInterface uses() uses(string $uses = null)
 * @method static mixed start() start(int|string $keyName, int $lifetime = null)
 * @method static void stop() stop(bool $stopBuffer = true)
 * @method static mixed getFrontend() getFrontend()
 * @method static array getOptions() getOptions()
 * @method static bool isFresh() isFresh()
 * @method static bool isStarted() isStarted()
 * @method static string setLastKey() setLastKey($lastKey)
 * @method static string getLastKey() getLastKey()
 * @method static mixed|null get() get(string $keyName, int $lifetime = null)
 * @method static bool save() save(int|string $keyName = null, string $content = null, int $lifetime = null, boolean $stopBuffer = true)
 * @method static bool delete() delete(int|string $keyName)
 * @method static array queryKeys() queryKeys(string $prefix = null)
 * @method static bool exists() exists(string $keyName = null, int $lifetime = null)
 */
class Cache extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Services::CACHE;
    }
}
