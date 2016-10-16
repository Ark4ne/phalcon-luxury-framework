<?php
namespace Test\Design;

use Luxury\Support\DesignPatterns\Strategy;
use Phalcon\Registry;

/**
 * Trait StrategyTest
 *
 * @package Test\Design
 */
class StrategyTest extends \PHPUnit_Framework_TestCase
{

    public function testWrong()
    {
        $instance = new class extends Strategy
        {
            protected $supported = [];
        };

        $this->setExpectedException(\RuntimeException::class,
            get_class($instance) . " : default unsupported. ");

        $instance->uses('default');
    }

    public function testMake()
    {
        $instance = new class extends Strategy
        {
            protected $supported = [
                Registry::class
            ];

            protected $default = Registry::class;
        };

        $this->assertInstanceOf(Registry::class, $instance->uses(Registry::class));
    }
}
