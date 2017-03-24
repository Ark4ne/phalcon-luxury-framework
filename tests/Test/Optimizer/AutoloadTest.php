<?php

namespace Test\Optimizer;

use Neutrino\Optimizer\Composer\Autoload;

/**
 * Class AutoloadTest
 *
 * @package Test
 */
class AutoloadTest extends \PHPUnit_Framework_TestCase
{

    public function testGetFiles()
    {
        $autoload = new Autoload(__DIR__ . DIRECTORY_SEPARATOR . 'fixture');

        $this->assertEquals([
            '123456' => __DIR__ . DIRECTORY_SEPARATOR . 'fixture/files_A.php',
            '234567' => __DIR__ . DIRECTORY_SEPARATOR . 'fixture/files_B.php',
            '345678' => __DIR__ . DIRECTORY_SEPARATOR . 'fixture/files_C.php',
        ], $autoload->getFiles());
    }

    public function testGetNamespaces()
    {
        $autoload = new Autoload(__DIR__ . DIRECTORY_SEPARATOR . 'fixture');

        $this->assertEquals([
            'Namespace'  => [__DIR__ . DIRECTORY_SEPARATOR . 'fixture/namespace/src'],
            'Namespace2' => [
                __DIR__ . DIRECTORY_SEPARATOR . 'fixture/namespace2/src',
                __DIR__ . DIRECTORY_SEPARATOR . 'fixture/namespace2/lib'
            ],
        ], $autoload->getNamespaces());
    }

    public function testGetPsr4()
    {
        $autoload = new Autoload(__DIR__ . DIRECTORY_SEPARATOR . 'fixture');

        $this->assertEquals([
            'Psr4' => [__DIR__ . DIRECTORY_SEPARATOR . 'fixture/Psr4/src'],
        ], $autoload->getPsr4());
    }

    public function testGetClassmap()
    {
        $autoload = new Autoload(__DIR__ . DIRECTORY_SEPARATOR . 'fixture');

        $this->assertEquals([
            'Class_A' => __DIR__ . DIRECTORY_SEPARATOR . 'fixture/Class_A.php',
            'Class_B' => __DIR__ . DIRECTORY_SEPARATOR . 'fixture/Class_B.php',
            'Class_C' => __DIR__ . DIRECTORY_SEPARATOR . 'fixture/Class_C.php',
        ], $autoload->getClassmap());
    }

    public function testGetNoFile()
    {
        $autoload = new Autoload(__DIR__ . DIRECTORY_SEPARATOR . 'fixture');

        $reflection = new \ReflectionClass(Autoload::class);
        $method     = $reflection->getMethod('getAutoloadContent');

        $method->setAccessible(true);

        $this->assertEquals([], $method->invoke($autoload, ''));
    }
}
