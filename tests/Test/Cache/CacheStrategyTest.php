<?php
namespace Test\Cache;

use Luxury\Support\Facades\Cache;
use Phalcon\Cache\Frontend\Base64;
use Phalcon\Cache\Frontend\Data;
use Phalcon\Cache\Frontend\Json;
use Test\TestCase\TestCase;
use Test\TestCase\UseCaches;

/**
 * Trait CacheStrategyTest
 *
 * @package Cache
 */
class CacheStrategyTest extends TestCase
{
    use UseCaches;

    public function testGoodCalled()
    {
        /** @var StubBackend $instance */
        $instance = Cache::uses('stub');

        $funcs = [
            'start'       => ['test'],
            'stop'        => ['test'],
            'getFrontend' => [],
            'getOptions'  => [],
            'isFresh'     => [],
            'isStarted'   => [],
            'setLastKey'  => ['test'],
            'getLastKey'  => [],
            'get'         => ['test'],
            'save'        => ['test'],
            'delete'      => ['test'],
            'queryKeys'   => ['test'],
            'exists'      => ['test'],
        ];

        foreach ($funcs as $func => $args) {
            Cache::$func(...$args);

            $this->assertTrue($instance->hasView($func));
        }
    }

    public function testUses()
    {
        $cacheInstance = Cache::uses();
        $this->assertInstanceOf(Data::class, $cacheInstance->getFrontend());

        $cacheInstance = Cache::uses('fast');
        $this->assertInstanceOf(Json::class, $cacheInstance->getFrontend());

        $cacheInstance = Cache::uses('slow');
        $this->assertInstanceOf(Base64::class, $cacheInstance->getFrontend());

        $cacheInstance = Cache::uses();
        $this->assertInstanceOf(Base64::class, $cacheInstance->getFrontend());
    }

    public function testSaveGetDelete()
    {
        Cache::save('test', 'data', 10);

        $this->assertEquals('data', Cache::get('test'));
        $this->assertEquals(['test'], Cache::queryKeys('test'));


        Cache::delete('test');

        $this->assertNull(Cache::get('test'));

        $this->assertEquals([], Cache::queryKeys('test'));
    }

    public function testQueryKeys()
    {
        $keys  = [];
        $datas = [];
        for ($i = 0; $i < 5; $i++) {
            Cache::save($keys[] = 'test' . $i, $datas[] = 'data' . $i, 10);
        }

        for ($i = 0; $i < 5; $i++) {
            Cache::exists('test' . $i);
        }

        foreach ($keys as $k => $key) {
            $this->assertEquals($datas[$k], Cache::get($key));
        }

        $cacheKeys = Cache::queryKeys('test');
        sort($cacheKeys);
        $this->assertEquals($keys, $cacheKeys);

        for ($i = 0; $i < 5; $i++) {
            Cache::delete('test' . $i);
        }

        $this->assertEquals([], Cache::queryKeys('test'));
    }

    public function testStartSave()
    {
        Cache::uses('output');

        $test = Cache::start('test');

        $this->assertNull($test);

        echo 'test';

        Cache::save();

        $test = Cache::start('test');

        $this->assertEquals('test', $test);
    }
}
