<?php

namespace Test\Micro;

use Test\TestCase\TestCase;

class MicroKernelTest extends TestCase
{
    use MicroTestCase;

    public function testAny()
    {

    }

    public function testRouteAbc()
    {
        $this->dispatch('get.test.abc');

        $this->assertEquals("get.test.abc", $this->getContent());
    }
}