<?php

namespace Neutrino\Foundation\Cli\Tasks;

use Neutrino\Cli\Output\Helper;
use Neutrino\Cli\Task;
use Phalcon\Cli\Router\Route;

/**
 * Class HelperTask
 *
 *  @package Neutrino\Foundation\Cli
 */
class HelperTask extends Task
{
    public function beforeExecuteRoute()
    {
        // Parent overload to prevent check the help option existence
    }

    public function mainAction()
    {
        $this->displayNeutrinoVersion();

        $infos = Helper::getTaskInfos(
            $this->getArg('task'),
            $this->getArg('action') . $this->dispatcher->getActionSuffix()
        );

        $route = $this->resolveRoute($this->getArg('task'), $this->getArg('action'));

        if (!empty($route)) {
            $this->line('Usage :');
            $this->info("\t" . $route->getPattern());
        }

        $this->line('Description :');
        $this->line("\t" . $infos['description']);

        if (arr_has($infos, 'arguments')) {
            $this->line('Arguments :');
            foreach ($infos['arguments'] as $argument) {
                $this->line("\t" . $argument);
            }
        }
        if (arr_has($infos, 'options')) {
            $this->line('Options :');
            foreach ($infos['options'] as $option) {
                $this->line("\t" . $option);
            }
        }
    }

    /**
     * @param $class
     * @param $action
     *
     * @return null|Route
     */
    private function resolveRoute($class, $action)
    {
        $routes = $this->router->getRoutes();

        $findedRoute = null;
        foreach ($routes as $route) {
            /** @var Route $route */

            $paths = $route->getPaths();

            if ($paths['task'] == $class) {
                if (arr_fetch($paths, 'action', 'main') == $action) {
                    $findedRoute = $route;
                    break;
                }
            }
        }

        if (!empty($findedRoute)) {
            return $findedRoute;
        }

        return null;
    }
}
