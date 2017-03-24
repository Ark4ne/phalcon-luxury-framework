<?php

namespace Neutrino;

use Neutrino\Support\Traits\ServicesProvidersRegistrable;
use Phalcon\Di;
use Phalcon\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\User\Module as PhalconModule;

/**
 * Class Module
 *
 * @package     Neutrino
 */
class Module extends PhalconModule implements ModuleDefinitionInterface
{

    /**
     * Return the Provider List to load.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Registers an autoloader related to the module
     *
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    public function registerAutoloaders(DiInterface $dependencyInjector = null){}

    /**
     * This methods registers the services to be used by the application
     *
     * @param \Phalcon\DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        foreach ($this->providers as $name => $provider) {
            if(is_string($name)){
                $service = new Di\Service($name, $provider, true);

                $di->setRaw($name, $service);
                $di->setRaw($provider, $service);

                continue;
            }

            /* @var \Neutrino\Interfaces\Providable $prv */
            $prv = new $provider();

            $prv->registering();
        }
    }
}
