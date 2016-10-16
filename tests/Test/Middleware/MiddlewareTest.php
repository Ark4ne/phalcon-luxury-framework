<?php

namespace Test\Middleware;

use Luxury\Constants\Events;
use Luxury\Foundation\Middleware\Application as ApplicationMiddleware;
use Luxury\Foundation\Middleware\Controller as ControllerMiddleware;
use Luxury\Foundation\Middleware\Disptacher as DisptacherMiddleware;
use Luxury\Middleware\AfterMiddleware;
use Luxury\Middleware\BeforeMiddleware;
use Luxury\Middleware\FinishMiddleware;
use Luxury\Middleware\InitMiddleware;
use Test\Stub\StubController;
use Test\TestCase\TestCase;
use Test\TestCase\TestListenable;
use Test\TestCase\TestListenize;

/**
 * Class MiddlewareTest
 *
 * @package Test
 */
class MiddlewareTest extends TestCase
{
    public function testControllerMiddleware()
    {
        // GIVEN
        $middleware = new TestControllerMiddlewareStub();

        $this->app->useImplicitView(false);

        $this->app->router->addGet('/', [
            'namespace'  => 'Test\Stub',
            'controller' => 'Stub',
            'action'     => 'index'
        ]);

        $this->app->attach($middleware);

        // WHEN
        $this->app->handle('/');

        // THEN
        $this->assertFalse($middleware->hasView('init'));
        $this->assertTrue($middleware->hasView('before'));
        $this->assertTrue($middleware->hasView('after'));
        $this->assertTrue($middleware->hasView('finish'));

        $this->assertEquals(0, count($middleware->getView('init')));
        $this->assertEquals(1, count($middleware->getView('before')));
        $this->assertEquals(1, count($middleware->getView('after')));
        $this->assertEquals(1, count($middleware->getView('finish')));
    }

    public function dataFiltereControllerMiddleware()
    {
        return [
            'only.indexAction'   => ['only', ['indexAction'], 0, 1, 1, 1],
            'except.indexAction' => ['except', ['indexAction'], 0, 0, 0, 0],
            'only.index'         => ['only', ['index'], 0, 0, 0, 0],
            'except.index'       => ['except', ['index'], 0, 1, 1, 1],
        ];
    }

    /**
     * @dataProvider dataFiltereControllerMiddleware
     *
     * @param $filter
     * @param $methods
     * @param $init
     * @param $before
     * @param $after
     * @param $finish
     */
    public function testFilteredControllerMiddleware(
        $filter,
        $methods,
        $init,
        $before,
        $after,
        $finish
    ) {
        // GIVEN
        $middleware = new TestControllerMiddlewareStub();

        StubController::$middlewares[] = [
            'middleware' => $middleware,
            'params'     => [$filter => $methods]
        ];

        $this->app->useImplicitView(false);

        $this->app->router->addGet('/', [
            'namespace'  => 'Test\Stub',
            'controller' => 'Stub',
            'action'     => 'index'
        ]);

        // WHEN
        $this->app->handle('/');

        // THEN
        $this->assertEquals($init, count($middleware->getView('init')));
        $this->assertEquals($before, count($middleware->getView('before')));
        $this->assertEquals($after, count($middleware->getView('after')));
        $this->assertEquals($finish, count($middleware->getView('finish')));
    }

    public function testDispatchMiddleware()
    {
        // GIVEN
        $middleware = new TestDispatchMiddlewareStub();

        $this->app->useImplicitView(false);

        $this->app->router->addGet('/', [
            'namespace'  => 'Test\Stub',
            'controller' => 'Stub',
            'action'     => 'index'
        ]);

        $this->app->attach($middleware);

        // WHEN
        $this->app->handle('/');

        // THEN
        $this->assertTrue($middleware->hasView('init'));
        $this->assertTrue($middleware->hasView('before'));
        $this->assertTrue($middleware->hasView('after'));
        $this->assertTrue($middleware->hasView('finish'));

        $this->assertEquals(1, count($middleware->getView('init')));
        $this->assertEquals(1, count($middleware->getView('before')));
        $this->assertEquals(1, count($middleware->getView('after')));
        $this->assertEquals(1, count($middleware->getView('finish')));
    }

    public function testApplicationMiddleware()
    {
        // GIVEN
        $middleware = new TestApplicationMiddlewareStub();

        $this->app->useImplicitView(false);

        $this->app->router->addGet('/', [
            'namespace'  => 'Test\Stub',
            'controller' => 'Stub',
            'action'     => 'index'
        ]);

        $this->app->attach($middleware);

        // WHEN
        $this->app->handle('/');

        // THEN
        $this->assertFalse($middleware->hasView('init'));
        $this->assertTrue($middleware->hasView('before'));
        $this->assertTrue($middleware->hasView('after'));
        $this->assertFalse($middleware->hasView('finish'));

        $this->assertEquals(0, count($middleware->getView('init')));
        $this->assertEquals(1, count($middleware->getView('before')));
        $this->assertEquals(1, count($middleware->getView('after')));
        $this->assertEquals(0, count($middleware->getView('finish')));
    }
}

class TestControllerMiddlewareStub extends ControllerMiddleware implements
    TestListenable,
    BeforeMiddleware,
    AfterMiddleware,
    FinishMiddleware
{
    use TestListenize, Middlewarize;
}

class TestDispatchMiddlewareStub extends DisptacherMiddleware implements
    TestListenable,
    InitMiddleware,
    BeforeMiddleware,
    AfterMiddleware,
    FinishMiddleware
{
    use TestListenize, Middlewarize;
}

class TestApplicationMiddlewareStub extends ApplicationMiddleware implements
    TestListenable,
    BeforeMiddleware,
    AfterMiddleware
{
    use TestListenize, Middlewarize;
}
