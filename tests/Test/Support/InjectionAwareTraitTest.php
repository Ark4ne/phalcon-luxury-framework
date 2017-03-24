<?php

namespace Test\Support;

use Neutrino\Support\Traits\InjectionAwareTrait;
use Phalcon\Di;
use Test\TestCase\TestCase;

/**
 * Class Test
 *
 * @package Test\Support
 */
class InjectionAwareTraitTest extends TestCase
{

    public function testMagicGet()
    {
        $mock = $this->getMockForTrait(InjectionAwareTrait::class);

        $services = [
            'dispatcher'         => \Phalcon\Mvc\Dispatcher::class,
            'router'             => \Phalcon\Mvc\Router::class,
            'url'                => \Phalcon\Mvc\Url::class,
            'modelsManager'      => \Phalcon\Mvc\Model\Manager::class,
            'modelsMetadata'     => \Phalcon\Mvc\Model\MetaData\Memory::class,
            'response'           => \Phalcon\Http\Response::class,
            'cookies'            => \Phalcon\Http\Response\Cookies::class,
            'request'            => \Phalcon\Http\Request::class,
            'filter'             => \Phalcon\Filter::class,
            'escaper'            => \Phalcon\Escaper::class,
            'security'           => \Phalcon\Security::class,
            'crypt'              => \Phalcon\Crypt::class,
            'annotations'        => \Phalcon\Annotations\Adapter\Memory::class,
            'flash'              => \Phalcon\Flash\Direct::class,
            'flashSession'       => \Phalcon\Flash\Session::class,
            'tag'                => \Phalcon\Tag::class,
            'session'            => \Phalcon\Session\Adapter\Files::class,
            'eventsManager'      => \Phalcon\Events\Manager::class,
            'transactionManager' => \Phalcon\Mvc\Model\Transaction\Manager::class,
            'assets'             => \Phalcon\Assets\Manager::class,
            'application'        => \Phalcon\Application::class,
            'config'             => \Phalcon\Config::class,
            'cache'              => \Neutrino\Cache\CacheStrategy::class,
        ];

        foreach ($services as $service => $class) {
            $this->assertInstanceOf($class, $mock->$service, $service);
        }
        foreach ($services as $service => $class) {
            $this->assertTrue(property_exists($mock, $service));
        }
    }
}