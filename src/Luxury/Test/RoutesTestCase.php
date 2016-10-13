<?php
/**
 * Created by PhpStorm.
 * User: gallegret
 * Date: 07/07/2016
 * Time: 15:08
 */

namespace Luxury\Test;

use Luxury\Constants\Services;

/**
 * Class RoutesTestCase
 *
 * @package Luxury\Test
 */
abstract class RoutesTestCase extends FuncTestCase
{
    private static $testedRoutes = [];

    /**
     * @return array
     */
    public function routesProvider()
    {
        $routes = $this->routes();

        $_routes = [];
        foreach ($routes as $route) {
            $_routes[$route[0]] = $route;
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
        // GIVEN
        $di = $this->getDI();
        /** @var \Phalcon\Mvc\Router $router */
        $router = $di->getShared(Services::ROUTER);

        $base = $di->getShared(Services::CONFIG)->application->baseUri;

        $route = preg_replace('#^/(.+)#', '$1', $route);

        $uri = $base . $route;

        // WHEN
        $_SERVER['REQUEST_METHOD'] = $method;
        $router->handle($uri);

        // THEN
        $this->assertEquals($expected, $router->wasMatched());

        if ($expected && $router->wasMatched()) {
            $matchedRoute = $router->getMatchedRoute();

            self::$testedRoutes[$matchedRoute->getPattern()] = $matchedRoute;
        }

        $controls = [
            'Controller' => $controller,
            'Action'     => $action
        ];

        foreach ($controls as $key => $value) {
            $key = 'get' . $key . 'Name';

            if (!$expected || ($expected && !is_null($value))) {
                $this->assertEquals($value, $router->$key());
            } elseif ($expected) {
                $this->assertTrue(is_string($router->$key()));
            }
        }

        $routeParams = $router->getParams();
        if ($expected && $router->wasMatched() && $params) {
            foreach ($params as $key => $value) {
                $this->assertArrayHasKey($key, $routeParams);
                $this->assertEquals($value, $routeParams[$key]);
            }
            foreach ($routeParams as $key => $value) {
                $this->assertArrayHasKey($key, $params);
                $this->assertEquals($value, $params[$key]);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getRoutes()
    {
        $routes = [];
        foreach ($this->globalApp()->router->getRoutes() as $route) {
            /** @var \Phalcon\Mvc\Router\Route $route */
            $routes[$route->getPattern()] = [$route];
        }

        return $routes;
    }

    /**
     * @test
     * @dataProvider      getRoutes
     * @depends           testRoutes
     *
     * @param \Phalcon\Mvc\Router\RouteInterface $route
     *
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
