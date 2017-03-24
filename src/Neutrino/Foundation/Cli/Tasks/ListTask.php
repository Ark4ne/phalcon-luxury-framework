<?php

namespace Neutrino\Foundation\Cli\Tasks;

use Neutrino\Cli\Output\Decorate;
use Neutrino\Cli\Output\Group;
use Neutrino\Cli\Output\Helper;
use Neutrino\Cli\Task;
use Phalcon\Cli\Router\Route;

/**
 * Class ListTask
 *
 *  @package Neutrino\Foundation\Cli
 */
class ListTask extends Task
{
    protected $reflections = [];
    protected $scanned     = [];
    protected $describes   = [];

    /**
     * List all commands available.
     *
     * @description List all commands available.
     */
    public function mainAction()
    {
        $this->displayNeutrinoVersion();

        $routes = $this->router->getRoutes();

        $delimiter = Route::getDelimiter();
        foreach ($routes as $route) {
            /** @var Route $route */
            // Default route
            $pattern = $route->getPattern();
            if ($pattern === "#^(?:$delimiter)?([a-zA-Z0-9\\_\\-]+)[$delimiter]{0,1}$#" ||
                $pattern === "#^(?:$delimiter)?([a-zA-Z0-9\\_\\-]+)$delimiter([a-zA-Z0-9\\.\\_]+)($delimiter.*)*$#"
            ) {
                continue;
            }

            $this->describeRoute($route);
        }

        $datas = [];

        foreach ($this->describes as $describe) {
            $datas[$describe['cmd']] = $describe['description'];
        }

        $this->notice('Available Commands :');

        (new Group($this->output, $datas))->display();
    }

    protected function describeRoute(Route $route)
    {
        $paths = $route->getPaths();

        $class = $paths['task'];

        $action = arr_fetch($paths, 'action', 'main') . $this->dispatcher->getActionSuffix();

        $this->scanned[$class . '::' . $action] = true;

        $compiled = Helper::describeRoutePattern($route);
        
        $this->describe($compiled, $class, $action);
    }

    protected function describe($pattern, $class, $action)
    {
        $infos = Helper::getTaskInfos($class, $action);

        if (!empty($infos['options'])) {
            $infos['options'] = implode(', ', $infos['options']);
        }
        if (!empty($infos['arguments'])) {
            $infos['arguments'] = implode(', ', $infos['arguments']);
        }

        $infos['cmd'] = Decorate::info($pattern);

        $this->describes[] = $infos;
    }
}
