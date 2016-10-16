<?php

namespace Test\Stub;

use Luxury\Support\Str;
use Phalcon\Cache\Backend;
use Phalcon\Cache\BackendInterface;
use Phalcon\Cache\Frontend\None;
use Phalcon\Registry;

/**
 * Class StubCache
 *
 * @package     Stub
 */
class StubCache extends Backend implements BackendInterface
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct()
    {
        parent::__construct(new None());

        $this->registry = new Registry();
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
        return isset($this->registry[$keyName]) ? $this->registry[$keyName] : null;
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
        $this->registry[$keyName] = $content;

        return true;
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
        unset($this->registry[$keyName]);

        return true;
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
        if ($prefix == null) {
            return $this->registry;
        }

        $found = [];
        foreach ($this->registry as $key => $value) {
            if (Str::startsWith($key, $prefix)) {
                $found[$key] = $value;
            }
        }

        return $found;
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
        return $this->registry->offsetExists($keyName);
    }
}
