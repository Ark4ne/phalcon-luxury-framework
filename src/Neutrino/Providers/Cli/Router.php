<?php

namespace Neutrino\Providers\Cli;

use Neutrino\Constants\Services;
use Neutrino\Cli\Router as NeutrinoRouter;
use Neutrino\Foundation\Cli\Tasks\ClearCompiledTask;
use Neutrino\Foundation\Cli\Tasks\ConfigCacheTask;
use Neutrino\Foundation\Cli\Tasks\ConfigClearTask;
use Neutrino\Foundation\Cli\Tasks\HelperTask;
use Neutrino\Foundation\Cli\Tasks\ListTask;
use Neutrino\Foundation\Cli\Tasks\OptimizeTask;
use Neutrino\Foundation\Cli\Tasks\RouteListTask;
use Neutrino\Foundation\Cli\Tasks\ViewClearTask;
use Neutrino\Providers\Provider;

/**
 * Class Router
 *
 *  @package Neutrino\Foundation\Bootstrap
 */
class Router extends Provider
{
    protected $name = Services::ROUTER;

    protected $shared = true;

    /**
     * @return \Phalcon\Cli\Router
     */
    protected function register()
    {
        $router = new NeutrinoRouter(false);

        $router->setDefaultTask(ListTask::class);

        $router->addTask('help ( .*)*', HelperTask::class);
        $router->addTask('list', ListTask::class);
        $router->addTask('optimize', OptimizeTask::class);
        $router->addTask('clear-compiled', ClearCompiledTask::class);
        $router->addTask('config:cache', ConfigCacheTask::class);
        $router->addTask('config:clear', ConfigClearTask::class);
        $router->addTask('route:list', RouteListTask::class);
        $router->addTask('view:clear', ViewClearTask::class);

        return $router;
    }
}
