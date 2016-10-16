<?php

namespace Test\TestCase;

use Luxury\Support\Facades\Cache;
use Test\Cache\StubBackend;

/**
 * Class UseCaches
 *
 * @package     Test\TestCase
 */
trait UseCaches
{
    protected static $cache_dir = __DIR__ . '/../../.data/';

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        global $config;

        $config = array_merge($config, [
            'cache' => [
                'default' => [
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
        ]);
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        global $config;

        $config = [];
    }

    public function setUp()
    {
        parent::setUp();

        $dir = static::$cache_dir;
        if (!is_dir($dir)) {
            if (!mkdir($dir)) {
                throw new \RuntimeException("Can't made .data directory.");
            }
        }
        // Clear File Cache
        $files = glob($dir . '/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                clearstatcache(true, $file);
                unlink($file); // delete file
            }
        }

        clearstatcache(true);
    }

    public function tearDown()
    {
        Cache::uses('default');

        parent::tearDown();

        $dir = static::$cache_dir;

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            clearstatcache(true, $dir . $item);
            unlink($dir . $item);
        }

        clearstatcache(true);
        rmdir($dir);
    }
}
