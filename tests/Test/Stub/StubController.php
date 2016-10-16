<?php

namespace Test\Stub;

use Luxury\Foundation\Controller;

/**
 * Class StubController
 *
 * @package     Stub
 */
class StubController extends Controller
{
    /**
     * @var array
     */
    public static $middlewares = [];

    /**
     * Event called on controller construction
     *
     * Register middleware here.
     */
    protected function onConstruct()
    {
        foreach (self::$middlewares as $middleware) {
            foreach ($middleware['params'] as $key => $method) {
                $this->middleware($middleware['middleware']->$key($method));
            }
        }
    }

    public function indexAction()
    {
    }
}
