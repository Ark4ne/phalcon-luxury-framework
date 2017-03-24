<?php

namespace Neutrino\Providers;

use Neutrino\Constants\Services;

use Phalcon\Mvc\Model\Metadata\Memory;

/**
 * Class ModelsMetaData
 *
 *  @package Neutrino\Foundation\Bootstrap
 */
class ModelsMetaData extends BasicProvider
{
    protected $class = Memory::class;

    protected $name = Services::MODELS_METADATA;

    protected $shared = true;

    protected $aliases = [Memory::class];
}
