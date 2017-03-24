<?php

namespace Neutrino\Providers;

use Neutrino\Cache\CacheStrategy;
use Neutrino\Constants\Services;
use Neutrino\Interfaces\Providable;
use Phalcon\Cache\BackendInterface;
use Phalcon\Cache\Frontend\None as FrontendNone;
use Phalcon\Cache\FrontendInterface;
use Phalcon\Di\Injectable;

/**
 * Class Cache
 *
 *  @package Neutrino\Providers
 */
class Cache extends Injectable implements Providable
{
    /**
     *
     */
    public function registering()
    {
        $di = $this->getDI();

        // Registering CacheStrategy
        $di->setShared(Services::CACHE, CacheStrategy::class);

        // Registering All Cache Driver
        $cache = $di->getShared(Services::CONFIG)->cache;

        foreach ($cache->stores as $name => $cache) {
            $di->setShared(
                Services::CACHE . '.' . $name,
                function () use ($cache) {
                    // Acceptable Driver (Backend)
                    $driver = $cache->driver;
                    if (empty($driver)) {
                        $driver = $cache->backend;
                    }
                    switch ($driver) {
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
                            $driverClass = '\Phalcon\Cache\Backend\\' . ucfirst($driver);
                            break;
                        default:
                            $driverClass = $driver;
                            if (!class_exists($driverClass)) {
                                $msg = empty($driver)
                                    ? 'Cache driver not set.'
                                    : "Cache driver $driver not implemented.";
                                throw new \RuntimeException($msg);
                            }
                    }

                    // Acceptable Adapter (Frontend)
                    $adapter = $cache->adapter;
                    if (empty($driver)) {
                        $adapter = $cache->frontend;
                    }
                    switch ($adapter) {
                        case 'Data':
                        case 'Json':
                        case 'File':
                        case 'Base64':
                        case 'Output':
                        case 'Igbinary':
                        case 'None':
                            $adapterClass = '\Phalcon\Cache\Frontend\\' . ucfirst($adapter);
                            break;
                        case null:
                            $adapterClass = FrontendNone::class;
                            break;
                        default:
                            $adapterClass = $adapter;

                            if (!class_exists($adapterClass)) {
                                throw new \RuntimeException("Cache adapter $adapter not implemented.");
                            }
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
}
