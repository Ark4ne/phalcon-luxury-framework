<?php

namespace Luxury\Providers;

use Luxury\Cache\CacheStrategy;
use Luxury\Constants\Services;
use Phalcon\Cache\BackendInterface;
use Phalcon\Cache\FrontendInterface;
use Phalcon\DiInterface;

/**
 * Class Cache
 *
 * @package Luxury\Providers
 */
class Cache extends Provider
{
    protected $name = Services::CACHE;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     */
    public function registering(DiInterface $di)
    {
        // Registering CacheStrategy
        $di->setShared($this->name, CacheStrategy::class);

        // Registering All Cache Driver
        $caches = $di->getShared(Services::CONFIG)->cache;

        foreach ($caches as $name => $cache) {
            $di->setShared(
                $this->name . '.' . $name,
                function () use ($cache) {
                    // Acceptable Driver (Backend)
                    $driver = $cache->driver;
                    if (empty($driver)) {
                        $driver = $cache->backend;
                    }
                    switch ($driver = ucfirst($driver)) {
                        case 'Aerospike':
                        case 'Apc':
                        case 'Database':
                        case 'Libmemcached':
                        case 'File':
                        case 'Memcache':
                        case 'Memory':
                        case 'Mongo':
                        case 'Redis':
                        case 'Wincache':
                        case 'Xcache':
                            $driverClass = '\Phalcon\Cache\Backend\\' . $driver;
                            break;
                        default:
                            $driverClass = $driver;
                    }

                    if (!class_exists($driverClass)) {
                        $msg = empty($driver)
                            ? 'Cache driver not set.'
                            : "Cache driver $driver not implemented.";
                        throw new \RuntimeException($msg);
                    }

                    // Acceptable Adapter (Frontend)
                    $adapter = $cache->adapter;
                    if (empty($driver)) {
                        $adapter = $cache->frontend;
                    }
                    switch ($adapter = ucfirst($adapter)) {
                        case 'Data':
                        case 'Json':
                        case 'File':
                        case 'Base64':
                        case 'Output':
                        case 'Igbinary':
                        case 'None':
                            $adapterClass = '\Phalcon\Cache\Frontend\\' . $adapter;
                            break;
                        case null:
                            $adapterClass = '\Phalcon\Cache\Frontend\None';
                            break;
                        default:
                            $adapterClass = $adapter;
                    }

                    if (!class_exists($adapterClass)) {
                        throw new \RuntimeException("Cache adapter $adapter not implemented.");
                    }

                    $options = isset($cache->options) ? (array)$cache->options : [];

                    $adapterInstance = new $adapterClass($options);

                    if (!($adapterInstance instanceof FrontendInterface)) {
                        throw new \RuntimeException("Cache adapter $adapter not implemente FrontendInterface.");
                    }

                    $driverInstance = new $driverClass($adapterInstance, $options);

                    if (!($driverInstance instanceof BackendInterface)) {
                        throw new \RuntimeException("Cache driver $adapter not implemente BackendInterface.");
                    }

                    return $driverInstance;
                }
            );
        }
    }

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return void
     */
    protected function register(DiInterface $di)
    {
    }
}
