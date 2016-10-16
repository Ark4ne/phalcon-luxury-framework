<?php

namespace Luxury\Providers;

use Luxury\Constants\Services;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Transaction\Manager;

/**
 * Class Model
 *
 * @package Luxury\Bootstrap\Services
 */
class ModelTransactionManager extends Provider
{
    protected $name = Services::TRANSACTION_MANAGER;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return \Phalcon\Mvc\Model\Transaction\Manager
     */
    protected function register(DiInterface $di)
    {
        return new Manager;
    }
}
