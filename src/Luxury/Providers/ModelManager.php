<?php

namespace Luxury\Providers;

use Luxury\Constants\Services;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Manager;

/**
 * Class ModelManager
 *
 * @package Luxury\Bootstrap\Services
 */
class ModelManager extends Provider
{
    protected $name = Services::MODELS_MANAGER;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return \Phalcon\Mvc\Model\Manager
     */
    protected function register(DiInterface $di)
    {
        return new Manager;
    }
}
