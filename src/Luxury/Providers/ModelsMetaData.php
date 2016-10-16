<?php

namespace Luxury\Providers;

use Luxury\Constants\Services;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Metadata\Memory;

/**
 * Class ModelsMetaData
 *
 * @package Luxury\Foundation\Bootstrap
 */
class ModelsMetaData extends Provider
{
    protected $name = Services::MODELS_METADATA;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return Memory
     */
    protected function register(DiInterface $di)
    {
        return new Memory;
    }
}
