<?php

namespace Test\Cache;

use Phalcon\Cache\Backend;
use Phalcon\Cache\BackendInterface;
use Phalcon\Registry;
use Test\TestCase\TestListenable;
use Test\TestCase\TestListenize;

/**
 * Class StubBackend
 *
 * @package     Test\Cache
 */
class StubBackend implements BackendInterface, TestListenable
{

    use TestListenize;

    private $cache;

    public function __construct()
    {
        $this->cache = new Registry();
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
        $this->view(__FUNCTION__, [$keyName, $lifetime]);
    }

    /**
     * Stops the frontend without store any cached content
     *
     * @param boolean $stopBuffer
     */
    public function stop($stopBuffer = true)
    {
        $this->view(__FUNCTION__, [$stopBuffer]);
    }

    /**
     * Returns front-end instance adapter related to the back-end
     *
     * @return mixed
     */
    public function getFrontend()
    {
        $this->view(__FUNCTION__, []);
    }

    /**
     * Returns the backend options
     *
     * @return array
     */
    public function getOptions()
    {
        $this->view(__FUNCTION__, []);
    }

    /**
     * Checks whether the last cache is fresh or cached
     *
     * @return bool
     */
    public function isFresh()
    {
        $this->view(__FUNCTION__, []);
    }

    /**
     * Checks whether the cache has starting buffering or not
     *
     * @return bool
     */
    public function isStarted()
    {
        $this->view(__FUNCTION__, []);
    }

    /**
     * Sets the last key used in the cache
     *
     * @param string $lastKey
     */
    public function setLastKey($lastKey)
    {
        $this->view(__FUNCTION__, [$lastKey]);
    }

    /**
     * Gets the last key stored by the cache
     *
     * @return string
     */
    public function getLastKey()
    {
        $this->view(__FUNCTION__, []);
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
        $this->view(__FUNCTION__, [$keyName, $lifetime]);
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
        $this->view(__FUNCTION__, [$keyName, $content, $lifetime, $stopBuffer]);
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
        $this->view(__FUNCTION__, [$keyName]);
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
        $this->view(__FUNCTION__, [$prefix]);
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
        $this->view(__FUNCTION__, [$keyName, $lifetime]);
    }
}
