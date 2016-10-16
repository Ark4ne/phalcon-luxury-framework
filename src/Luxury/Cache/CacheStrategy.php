<?php
namespace Luxury\Cache;

use Luxury\Constants\Services;
use Luxury\Support\DesignPatterns\Strategy;
use Luxury\Support\Traits\InjectionAwareTrait;
use Phalcon\Cache\BackendInterface;
use Phalcon\Di\InjectionAwareInterface;

/**
 * Class CacheService
 *
 * @package Luxury\Cache
 *
 * @method BackendInterface uses(string $use = null)
 */
class CacheStrategy extends Strategy implements InjectionAwareInterface, BackendInterface
{
    use InjectionAwareTrait;

    protected $default = 'default';

    /**
     * CacheStrategy constructor.
     */
    public function __construct()
    {
        $caches = $this->getDI()->getShared(Services::CONFIG)->cache;

        foreach ($caches as $name => $cache) {
            $this->supported[] = $name;
        }
    }

    /**
     * @inheritdoc
     */
    protected function make($use)
    {
        return $this->getDI()->getShared(Services::CACHE . '.' . $use);
    }

    /**
     * Starts a cache. The keyname allows to identify the created fragment
     *
     * @param int|string $keyName
     * @param int        $lifetime
     *
     * @return mixed
     */
    public function start($keyName, $lifetime = null)
    {
        return $this->uses()->start($keyName, $lifetime);
    }

    /**
     * Stops the frontend without store any cached content
     *
     * @param boolean $stopBuffer
     */
    public function stop($stopBuffer = true)
    {
        return $this->uses()->stop($stopBuffer);
    }

    /**
     * Returns front-end instance adapter related to the back-end
     *
     * @return mixed
     */
    public function getFrontend()
    {
        return $this->uses()->getFrontend();
    }

    /**
     * Returns the backend options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->uses()->getOptions();
    }

    /**
     * Checks whether the last cache is fresh or cached
     *
     * @return bool
     */
    public function isFresh()
    {
        return $this->uses()->isFresh();
    }

    /**
     * Checks whether the cache has starting buffering or not
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->uses()->isStarted();
    }

    /**
     * Sets the last key used in the cache
     *
     * @param string $lastKey
     */
    public function setLastKey($lastKey)
    {
        return $this->uses()->setLastKey($lastKey);
    }

    /**
     * Gets the last key stored by the cache
     *
     * @return string
     */
    public function getLastKey()
    {
        return $this->uses()->getLastKey();
    }

    /**
     * Returns a cached content
     *
     * @param string $keyName
     * @param int    $lifetime
     *
     * @return mixed|null
     */
    public function get($keyName, $lifetime = null)
    {
        return $this->uses()->get($keyName, $lifetime);
    }

    /**
     * Stores cached content into the file backend and stops the frontend
     *
     * @param int|string $keyName
     * @param string     $content
     * @param int        $lifetime
     * @param boolean    $stopBuffer
     *
     * @return bool
     */
    public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true)
    {
        return $this->uses()->save($keyName, $content, $lifetime, $stopBuffer);
    }

    /**
     * Deletes a value from the cache by its key
     *
     * @param int|string $keyName
     *
     * @return boolean
     */
    public function delete($keyName)
    {
        return $this->uses()->delete($keyName);
    }

    /**
     * Query the existing cached keys
     *
     * @param string $prefix
     *
     * @return array
     */
    public function queryKeys($prefix = null)
    {
        return $this->uses()->queryKeys($prefix);
    }

    /**
     * Checks if cache exists and it hasn't expired
     *
     * @param string $keyName
     * @param int    $lifetime
     *
     * @return boolean
     */
    public function exists($keyName = null, $lifetime = null)
    {
        return $this->uses()->exists($keyName, $lifetime);
    }
}
