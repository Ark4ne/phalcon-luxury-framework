<?php

namespace Test\Cli\Tasks;

use Neutrino\Cli\Output\ConsoleOutput;
use Neutrino\Cli\Output\Helper;
use Neutrino\Constants\Services;
use Neutrino\Foundation\Cli\Tasks\HelperTask;
use Neutrino\Foundation\Cli\Tasks\ListTask;
use Neutrino\Foundation\Cli\Tasks\OptimizeTask;
use Phalcon\Cli\Dispatcher;
use Phalcon\Cli\Router\Route;
use Phalcon\Events\Manager;
use Test\Stub\StubKernelCli;
use Test\TestCase\TestCase;

class HelperTaskTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        // Force Enable Decoration for windows
        putenv('TERM=xterm');
    }

    public function tearDown()
    {
        parent::tearDown();

        // Force Enable Decoration for windows
        putenv('TERM=');
    }

    protected static function kernelClassInstance()
    {
        return StubKernelCli::class;
    }

    public function dataResolveRoute()
    {
        return [
            [new Route('list', ['task' => ListTask::class, 'action' => null]), ListTask::class, 'main'],
            [new Route('optimize', ['task' => OptimizeTask::class, 'action' => null]), OptimizeTask::class, 'main']
        ];
    }

    /**
     * @dataProvider dataResolveRoute
     */
    public function testResolveRoute($expected, $class, $action)
    {
        $dispatcher = $this->mockService(Services::DISPATCHER, Dispatcher::class, true);

        $dispatcher->expects($this->any())->method('getEventsManager')->willReturn($this->createMock(Manager::class));
        $dispatcher->expects($this->any())->method('getActionSuffix')->willReturn('Action');

        $task = new HelperTask();

        /** @var Route $route */
        $route = $this->invokeMethod($task, 'resolveRoute', [$class, $action]);

        $this->assertEquals($expected->getPattern(), $route->getPattern());
        $this->assertEquals($expected->getPaths(), $route->getPaths());
    }

    public function dataMainAction()
    {
        putenv('TERM=xterm');
        return [
            [HelperTask::class, 'main', [
                'info'  => ['exactly' => 1, 'consecutive' => [["\t" . 'help ( .*)*']]],
                'write' => ['exactly' => 4, 'consecutive' => [
                    [Helper::neutrinoVersion() . PHP_EOL, true], ['Usage :', true], ['Description :', true], ["\t", true]
                ]],
            ]],
            [ListTask::class, 'main', [
                'info'  => ['exactly' => 1, 'consecutive' => [["\t" . 'list']]],
                'write' => ['exactly' => 4, 'consecutive' => [
                    [Helper::neutrinoVersion() . PHP_EOL, true], ['Usage :', true], ['Description :', true], ["\t" . 'List all commands available.', true]
                ]]
            ]],
            [OptimizeTask::class, 'main', [
                'info'  => ['exactly' => 1],
                'write' => ['exactly' => 6, 'consecutive' => [
                    [Helper::neutrinoVersion() . PHP_EOL, true],
                    ['Usage :', true],
                    ['Description :', true],
                    ["\t" . 'Optimize the autoloader.', true],
                    ['Options :', true],
                    ["\t" . '-m, --memory: Optimize memory.', true],
                ]]
            ]],
        ];
    }

    /**
     * @dataProvider dataMainAction
     */
    public function testMainAction($class, $action, $expected)
    {
        $dispatcher = $this->mockService(Services::DISPATCHER, Dispatcher::class, true);

        $dispatcher->expects($this->any())->method('getEventsManager')->willReturn($this->createMock(Manager::class));
        $dispatcher->expects($this->any())->method('getActionSuffix')->willReturn('Action');

        $dispatcher->expects($this->any())
            ->method('getParams')
            ->willReturn([
                'task'   => $class,
                'action' => $action,
            ]);

        $mock = $this->createMock(ConsoleOutput::class);
        foreach ($expected as $func => $params) {
            $method = $mock->expects($this->exactly($params['exactly']))
                ->method($func);

            if (!empty($params['consecutive'])) {
                $method->withConsecutive(...$params['consecutive']);
            }
        }

        $task = new HelperTask();

        $this->setValueProperty($task, 'output', $mock);

        $task->mainAction();
    }
}
