<?php

/**
 * Created by PhpStorm.
 * User: xlzi590
 * Date: 24/11/2016
 * Time: 15:21
 */
class DotenvTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        putenv('APP_ENV=test');
    }

    public function tearDown()
    {
        parent::tearDown();

        putenv('APP_ENV=test');
    }

    public function testWithNoEnvFileInGivenPath()
    {
        $env    = $_ENV;
        $server = $_SERVER;

        $this->assertFalse(\Neutrino\Dotenv\Loader::load(__DIR__));

        $this->assertEquals($env, $_ENV);
        $this->assertEquals($server, $_SERVER);
    }

    public function dataValidLoad()
    {
        return [
            ['local', [
                'APP_ENV'          => 'local',
                'KEY_1'            => 'VALUE_1',
                'KEY_2'            => 'VALUE_2',
                'BOOL_KEY_TRUE_1'  => 'true',
                'BOOL_KEY_TRUE_2'  => '(true)',
                'BOOL_KEY_FALSE_1' => 'false',
                'BOOL_KEY_FALSE_2' => '(false)',
                'EMPTY_KEY_1'      => 'empty',
                'EMPTY_KEY_2'      => '(empty)',
                'NULL_KEY_1'       => 'null',
                'NULL_KEY_2'       => '(null)'
            ]],
            ['testing', [
                'APP_ENV' => 'testing',
                'KEY_1'   => 'TESTING_VALUE_1',
                'KEY_2'   => 'VALUE_2',
                'KEY_3'   => 'TESTING_VALUE_3',
            ]]
        ];
    }

    /**
     * @dataProvider dataValidLoad
     */
    public function testValidLoad($env, $expected)
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'fixture' . DIRECTORY_SEPARATOR . $env;

        $this->assertTrue(\Neutrino\Dotenv\Loader::load($path));

        foreach ($expected as $key => $value) {
            if (function_exists('putenv')) {
                $this->assertEquals(getenv($key), $value);
            }

            $this->assertArrayHasKey($key, $_ENV);

            $this->assertEquals($_ENV[$key], $value);
        }
    }

    /**
     * @expectedException \Neutrino\Dotenv\Exception\InvalidFileException
     */
    public function testWrongValue()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'fixture' . DIRECTORY_SEPARATOR . 'failing';

        \Neutrino\Dotenv\Loader::load($path);
    }

    public function testGetenv()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'fixture' . DIRECTORY_SEPARATOR . 'local';

        $this->assertTrue(\Neutrino\Dotenv\Loader::load($path));

        $expected = [
            'APP_ENV'          => 'local',
            'KEY_1'            => 'VALUE_1',
            'KEY_2'            => 'VALUE_2',
            'BOOL_KEY_TRUE_1'  => true,
            'BOOL_KEY_TRUE_2'  => true,
            'BOOL_KEY_FALSE_1' => false,
            'BOOL_KEY_FALSE_2' => false,
            'EMPTY_KEY_1'      => '',
            'EMPTY_KEY_2'      => '',
            'NULL_KEY_1'       => null,
            'NULL_KEY_2'       => null
        ];

        foreach ($expected as $key => $value) {
            $this->assertEquals($value, \Neutrino\Dotenv::env($key));
        }

        $this->assertEquals(null, \Neutrino\Dotenv::env("no_exist_key"));
        $this->assertEquals('default', \Neutrino\Dotenv::env("no_exist_key", 'default'));
    }

    public function testGetenvCache()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'fixture' . DIRECTORY_SEPARATOR . 'local';

        $this->assertTrue(\Neutrino\Dotenv\Loader::load($path));

        $this->assertEquals('local', \Neutrino\Dotenv::env("APP_ENV"));

        \Neutrino\Dotenv::put("APP_ENV", 'test');

        $this->assertEquals('test', \Neutrino\Dotenv::env("APP_ENV"));
        $this->assertEquals('test', \Neutrino\Dotenv::env("APP_ENV", true));
    }
}
