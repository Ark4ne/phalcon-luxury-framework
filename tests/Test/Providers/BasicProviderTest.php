<?php

namespace Test\Providers;

use Neutrino\Providers\BasicProvider;
use Test\TestCase\TestCase;

class BasicProviderTest extends TestCase
{

    /**
     * @expectedException \RuntimeException
     */
    public function testNoName()
    {
        new StubWrongNameBasicProvider;
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNoClass()
    {
        new StubWrongClassBasicProvider;
    }

    public function testRegister()
    {
        $provider = new StubRegisterBasicProvider;

        $provider->registering();

        $this->assertTrue($this->getDI()->has('test'));
        $this->assertInstanceOf(StubRegisterBasicProvider::class, $this->getDI()->get('test'));
    }
}

class StubWrongNameBasicProvider extends BasicProvider
{
};

class StubWrongClassBasicProvider extends BasicProvider
{
    protected $name = 'test';
};
class StubRegisterBasicProvider extends BasicProvider
{
    protected $name = 'test';
    protected $class = StubRegisterBasicProvider::class;
};
