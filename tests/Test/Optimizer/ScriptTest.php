<?php

namespace Test\Optimizer;

use Neutrino\Optimizer\Composer\Script;

/**
 * Class ScriptTest
 *
 * @package Test
 */
class ScriptTest extends \PHPUnit_Framework_TestCase
{
    public function dataGetComposerCmd()
    {
        return [
            [__DIR__, 'composer'],
            [__DIR__ . '/fixture', (getenv('PHP_BINARY') ?: PHP_BINARY) . ' composer.phar'],
        ];
    }

    /**
     * @dataProvider dataGetComposerCmd
     */
    public function testGetComposerCmd($basePath, $composerCmd)
    {
        $script = new Script($basePath);

        $reflection = new \ReflectionClass(Script::class);
        $method     = $reflection->getMethod('getComposerCmd');

        $method->setAccessible(true);

        $this->assertEquals($composerCmd, $method->invoke($script));
    }

    public function testBuildComposerCmd()
    {
        $script = new Script(__DIR__);

        $reflection = new \ReflectionClass(Script::class);
        $method     = $reflection->getMethod('buildComposerCmd');

        $method->setAccessible(true);

        if (DIRECTORY_SEPARATOR === '\\') {
            $cmd = 'cmd /Q /C "composer action --option --option_3=value" > NUL 2> NUL';
        } else {
            $cmd = 'composer action --option --option_3=value 1> /dev/null 2> /dev/null';
        }

        $this->assertEquals($cmd, $method->invoke($script, 'action', [
            'option'   => true,
            'option_2' => false,
            'option_3' => 'value'
        ]));
    }
}
