<?php

namespace Neutrino\Test;

use Neutrino\Dotenv;
use Neutrino\Providers\Http\Router;
use Neutrino\Support\Facades\Facade;
use Neutrino\Test\Helpers\RoutesTrait;
use Phalcon\Di;

/**
 * Class RoutesTestCase
 *
 * @package Neutrino\Test
 */
abstract class RoutesTestCase extends FuncTestCase
{
    use RoutesTrait;

    /**
     * Return the application route
     *
     * @return array
     */
    public function getApplicationRoutes()
    {
        $di = new Di();
        Di::setDefault($di);

        Facade::clearResolvedInstances();
        Facade::setDependencyInjection($di);

        (new Router())->registering();

        require Dotenv::env('BASE_PATH') . '/routes/http.php';

        $routes = [];
        foreach ($di->getShared('router')->getRoutes() as $route) {
            /** @var \Phalcon\Mvc\Router\Route $route */
            $routes[$route->getPattern()] = [$route];
        }

        Facade::clearResolvedInstances();

        return $routes;
    }

    /**
     * @param string $route      Route Url
     * @param string $method     Http Method
     * @param bool   $expected   Route match excepted
     * @param string $controller Controller excepted
     * @param string $action     Action excepted
     * @param array  $params     Params passed to the route
     *
     * @return array
     */
    public function formatDataRoute(
        $route,
        $method,
        $expected,
        $controller = null,
        $action = null,
        array $params = null
    ) {
        return [$route, $method, $expected, $controller, $action, $params];
    }

    /**
     * @return array
     */
    public function routesProvider()
    {
        $routes = $this->routes();

        $_routes = [];
        foreach ($routes as $route) {
            $key =
                $route[1] . '-' . $route[0] . '-' . ($route[2] ? 'true' : 'false') . '-' . substr(md5(uniqid('', true)),
                    0, 6);

            $_routes[$key] = $route;
        }

        return $_routes;
    }

    /**
     * @test
     * @dataProvider routesProvider
     *
     * @param       $route
     * @param       $method
     * @param       $expected
     * @param null  $controller
     * @param null  $action
     * @param array $params
     */
    public function testRoutes(
        $route,
        $method,
        $expected,
        $controller = null,
        $action = null,
        array $params = null
    ) {
        $this->assertRoute($route, $method, $expected, $controller, $action, $params);
    }

    /**
     * @test
     * @dataProvider      getApplicationRoutes
     * @depends           testRoutes
     *
     * @param \Phalcon\Mvc\Router\RouteInterface $route
     */
    public function testRoutesTested($route = null)
    {
        if (!array_key_exists($route->getPattern(), self::$testedRoutes)) {
            $this->markTestIncomplete('Route "' . $route->getPattern() . '" has not been testing');

            return;
        }

        $this->assertArrayHasKey($route->getPattern(), self::$testedRoutes);
        $this->assertEquals(
            $this->routeToArray($route),
            $this->routeToArray(self::$testedRoutes[$route->getPattern()])
        );
    }

    /**
     * Return the routes to test
     *
     * @return array[]
     */
    abstract protected function routes();

    /**
     * @param \Phalcon\Mvc\Router\RouteInterface $route
     *
     * @return array
     */
    private function routeToArray($route)
    {
        return [
            'HttpMethods'     => $route->getHttpMethods(),
            'Hostname'        => $route->getHostname(),
            'Name'            => $route->getName(),
            'Pattern'         => $route->getPattern(),
            'CompiledPattern' => $route->getCompiledPattern(),
            'Paths'           => $route->getPaths(),
        ];
    }
}
