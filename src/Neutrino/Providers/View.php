<?php

namespace Neutrino\Providers;

use Neutrino\Constants\Services;
use Neutrino\Interfaces\Providable;
use Neutrino\View\Engine\Extensions\PhpFunction as PhpFunctionExtension;
use Phalcon\Assets\Manager as AssetsManager;
use Phalcon\Di\Injectable;
use Phalcon\DiInterface;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Tag;

/**
 * Class View
 *
 *  @package Neutrino\Foundation\Bootstrap
 */
class View extends Injectable implements Providable
{
    /**
     * @inheritdoc
     */
    public function registering()
    {
        $di = $this->getDI();

        $di->setShared(Services::TAG, Tag::class);
        $di->setShared(Tag::class, Tag::class);
        $di->setShared(Services::ASSETS, AssetsManager::class);
        $di->setShared(AssetsManager::class, AssetsManager::class);

        $di->setShared(Services::VIEW, function () {
            /** @var DiInterface $this */

            $view = new \Phalcon\Mvc\View();

            $view->setViewsDir($this->getShared(Services::CONFIG)->view->views_dir);

            $view->registerEngines([
                '.volt'  => function ($view, $di) {
                    /* @var \Phalcon\Di $di */
                    $volt = new VoltEngine($view, $di);

                    $volt->setOptions([
                        'compiledPath'      => $di->getShared(Services::CONFIG)->view->compiled_path,
                        'compiledSeparator' => '_'
                    ]);
                    $volt->getCompiler()->addExtension(new PhpFunctionExtension());

                    return $volt;
                },
                '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
            ]);

            return $view;
        });
    }
}
