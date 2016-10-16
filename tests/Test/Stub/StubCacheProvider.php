<?php

namespace Test\Stub;

use Luxury\Constants\Services;
use Luxury\Interfaces\Providable;
use Luxury\Providers\Provider;
use Phalcon\DiInterface;

/**
 * Class StubCacheProvider
 *
 * @package     Stub
 */
class StubCacheProvider extends Provider
{

    protected $name = Services::CACHE;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return void
     */
    public function register(DiInterface $di)
    {
       // $di->setShared(Services::CACHE, function () {
            return new StubCache();
       // });
    }
}
