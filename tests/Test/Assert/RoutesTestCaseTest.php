<?php

namespace Test\Assert;

use Neutrino\Dotenv;
use Neutrino\Http\Standards\Method;
use Neutrino\Test\RoutesTestCase;
use Phalcon\Mvc\Router\Route;
use Test\Stub\StubController;
use Test\Stub\StubRouteTestCase;
use Test\TestCase\TestCase;
use Test\TestCase\TraitTestCase;

class RoutesTestCaseTest extends TestCase
{
    use TraitTestCase;

    /**
     * @return StubRouteTestCase
     */
    public function getStub()
    {
        return new StubRouteTestCase;
    }

    public function testRouteProvider()
    {
        $routesTestCase = $this->getStub();

        $expecteds = [
            'GET-/-true'               => ['/', 'GET', true, null, null, null],
            'POST-/-false'             => ['/', 'POST', false, null, null, null],
            'GET-/something/:int-true' => ['/something/:int', 'GET', true, 'index', StubController::class, [1]],
        ];

        $routes = $routesTestCase->routesProvider();

        $routesKeys = array_keys($routes);
        $routesValues = array_values($routes);

        $i = 0;
        foreach ($expecteds as $key => $expected) {
            $this->assertStringStartsWith($key, $routesKeys[$i]);
            $this->assertEquals($expected, $routesValues[$i]);

            $i++;
        }
    }

    public function testGetRoutes()
    {
        Dotenv::put('BASE_PATH', __DIR__ . '/../Stub');

        $routesTestCase = $this->getStub();

        $expectedRoutes = [
            '/get'      => [Route::class],
            '/post'     => [Route::class],
            '/u/:int'   => [Route::class],
            '/get-head' => [Route::class],
        ];

        $appRoutes = $routesTestCase->getApplicationRoutes();

        foreach ($expectedRoutes as $route => $expectedRoute) {
            $this->assertArrayHasKey($route, $appRoutes);
            $this->assertInstanceOf($expectedRoute[0], $appRoutes[$route][0]);
        }
    }

    public function testTestRoutes()
    {
        $routesTestCase = $this->getStub();

        $routesTestCase->testRoutes('', Method::GET, true);
        $routesTestCase->testRoutes('', Method::GET, true, 'Stub', 'index');
        $routesTestCase->testRoutes('/parameted/param_1', Method::GET, true, 'Stub', 'index', ['tags' => 'param_1']);

        $this->assertEquals([
            ['', Method::GET, true, null, null, null],
            ['', Method::GET, true, 'Stub', 'index', null],
            ['/parameted/param_1', Method::GET, true, 'Stub', 'index', ['tags' => 'param_1']],
        ], $this->getValueProperty($routesTestCase, 'testedRoutes', RoutesTestCase::class));
    }
}
