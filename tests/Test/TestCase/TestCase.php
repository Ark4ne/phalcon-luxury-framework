<?php

namespace Test\TestCase;

use Luxury\Test\FuncTestCase;

/**
 * Class TestCase
 */
abstract class TestCase extends FuncTestCase
{
    use TraitTestCase;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        global $config;

        $config = array_merge($config, [
            'cache' => []
        ]);
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        global $config;

        $config = [];
    }
}
