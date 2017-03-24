<?php

namespace Test\Cli\Tasks;

use Neutrino\Cli\Output\ConsoleOutput;
use Neutrino\Cli\Output\Decorate;
use Neutrino\Constants\Services;
use Neutrino\Dotenv;
use Neutrino\Foundation\Cli\Tasks\RouteListTask;
use Phalcon\Cli\Dispatcher;
use Phalcon\Events\Manager;
use Phalcon\Cli\Router;
use Test\Stub\StubKernelCli;
use Test\TestCase\TestCase;

class RouteListTaskTest extends TestCase
{
    protected static function kernelClassInstance()
    {
        return StubKernelCli::class;
    }

    public function testGetHttpRoute()
    {
        Dotenv::put('BASE_PATH', __DIR__ . '/../../Stub');
        $expectedRoutes = [
            ['pattern' => '/get', 'paths' => ['controller' => 'Stub', 'action' => 'index']]
        ];

        $eventManager = $this->createMock(Manager::class);

        $dispatcher = $this->mockService(Services::DISPATCHER, Dispatcher::class, true);

        $dispatcher->expects($this->any())->method('getEventsManager')->willReturn($eventManager);
        $dispatcher->expects($this->any())->method('getActionSuffix')->willReturn('Action');

        $task = new RouteListTask();

        /** @var Router\Route[] $routes */
        $routes = $this->invokeMethod($task, 'getHttpRoutes', []);

        $this->assertInstanceOf(Router::class, $this->getDI()->getShared(Services::ROUTER));

        foreach ($expectedRoutes as $key => $expectedRoute) {
            $route = $routes[$key];

            $this->assertEquals($expectedRoute['pattern'], $route->getPattern());
            $this->assertEquals($expectedRoute['paths'], $route->getPaths());
        }
    }

    public function testMainAction()
    {
        Dotenv::put('BASE_PATH', __DIR__ . '/../../Stub');

        $expected = [
            'write' => ['exactly' => 8, 'consecutive' => [
                ['+--------+------+----------+-----------+--------------------------------------------+-------------------------------+'],
                ['| DOMAIN | NAME | METHOD   | PATTERN   | ACTION                                     | MIDDLEWARE                    |'],
                ['+--------+------+----------+-----------+--------------------------------------------+-------------------------------+'],
                ['|        |      | GET      | /get      | App\Http\Controllers\StubController::index |                               |'],
                ['|        |      | POST     | /post     | Test\Stub\StubController::index            |                               |'],
                ['|        |      | GET      | /u/'.Decorate::notice('{user}').' | Test\Stub\StubController::index            |                               |'],
                ['|        |      | GET|HEAD | /get-head | Test\Stub\StubController::index            | '.\Neutrino\Http\Middleware\Csrf::class.' |'],
                ['+--------+------+----------+-----------+--------------------------------------------+-------------------------------+'],
            ]]
        ];

        $eventManager = $this->createMock(Manager::class);

        $dispatcher = $this->mockService(Services::DISPATCHER, Dispatcher::class, true);

        $dispatcher->expects($this->any())->method('getEventsManager')->willReturn($eventManager);
        $dispatcher->expects($this->any())->method('getActionSuffix')->willReturn('Action');

        $mock = $this->createMock(ConsoleOutput::class);
        foreach ($expected as $func => $params) {
            $method = $mock->expects($this->exactly($params['exactly']))
                ->method($func);

            if (!empty($params['consecutive'])) {
                $method->withConsecutive(...$params['consecutive']);
            }
        }

        $task = new RouteListTask();

        $this->setValueProperty($task, 'output', $mock);

        $task->mainAction();
    }

    public function testMainActionNoSubstitution()
    {
        Dotenv::put('BASE_PATH', __DIR__ . '/../../Stub');

        $expected = [
            'write' => ['exactly' => 8, 'consecutive' => [
                ['+--------+------+----------+-----------+--------------------------------------------+-------------------------------+'],
                ['| DOMAIN | NAME | METHOD   | PATTERN   | ACTION                                     | MIDDLEWARE                    |'],
                ['+--------+------+----------+-----------+--------------------------------------------+-------------------------------+'],
                ['|        |      | GET      | /get      | App\Http\Controllers\StubController::index |                               |'],
                ['|        |      | POST     | /post     | Test\Stub\StubController::index            |                               |'],
                ['|        |      | GET      | /u/:int   | Test\Stub\StubController::index            |                               |'],
                ['|        |      | GET|HEAD | /get-head | Test\Stub\StubController::index            | '.\Neutrino\Http\Middleware\Csrf::class.' |'],
                ['+--------+------+----------+-----------+--------------------------------------------+-------------------------------+'],
            ]]
        ];

        $eventManager = $this->createMock(Manager::class);

        $dispatcher = $this->mockService(Services::DISPATCHER, Dispatcher::class, true);

        $dispatcher->expects($this->any())->method('getEventsManager')->willReturn($eventManager);
        $dispatcher->expects($this->any())->method('getActionSuffix')->willReturn('Action');
        $dispatcher->expects($this->any())->method('getOptions')->willReturn(['no-substitution' => true]);

        $mock = $this->createMock(ConsoleOutput::class);
        foreach ($expected as $func => $params) {
            $method = $mock->expects($this->exactly($params['exactly']))
                ->method($func);

            if (!empty($params['consecutive'])) {
                $method->withConsecutive(...$params['consecutive']);
            }
        }

        $task = new RouteListTask();

        $this->setValueProperty($task, 'output', $mock);

        $task->mainAction();
    }
}
