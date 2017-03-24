<?php

namespace Test\TestCase;

use Neutrino\Support\Facades\Cache;
use Test\Cache\StubBackend;

/**
 * Class UseCaches
 *
 * @package     Test\TestCase
 */
trait UseCaches
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        global $config;

        $config = array_merge($config, [
            'cache' => [
                'default' => 'memory',
                'stores' => [
                    'memory' => [
                        'driver' => \Phalcon\Cache\Backend\Memory::class,
                        'adapter' => 'None',
                    ],
                    'file'   => [
                        'adapter' => 'Data', // Files, Memcache, Libmemcached, Redis
                        'driver'  => 'File', // Files, Memcache, Libmemcached, Redis
                        'options' => ['cacheDir' => static::$cache_dir],
                    ],
                    'fast'    => [
                        'adapter' => 'Json', // Files, Memcache, Libmemcached, Redis
                        'driver'  => 'File', // Files, Memcache, Libmemcached, Redis
                        'options' => ['cacheDir' => static::$cache_dir],
                    ],
                    'slow'    => [
                        'adapter' => 'Base64', // Files, Memcache, Libmemcached, Redis
                        'driver'  => 'File', // Files, Memcache, Libmemcached, Redis
                        'options' => ['cacheDir' => static::$cache_dir],
                    ],
                    'output'  => [
                        'adapter' => 'Output', // Files, Memcache, Libmemcached, Redis
                        'driver'  => 'File', // Files, Memcache, Libmemcached, Redis
                        'options' => ['cacheDir' => static::$cache_dir],
                    ],
                    'stub'    => [
                        'adapter' => 'Data', // Files, Memcache, Libmemcached, Redis
                        'driver'  => StubBackend::class, // Files, Memcache, Libmemcached, Redis
                        'options' => ['cacheDir' => static::$cache_dir],
                    ]
                ]
            ]
        ]);
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        global $config;

        $config = [];
    }

    public function tearDown()
    {
        Cache::uses('file');

        parent::tearDown();
    }
}
