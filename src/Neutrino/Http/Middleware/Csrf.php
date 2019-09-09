<?php

namespace Neutrino\Http\Middleware;

use Neutrino\Exceptions\TokenMismatchException;
use Neutrino\Foundation\Middleware\Controller;
use Neutrino\Interfaces\Middleware\BeforeInterface;
use Phalcon\Events\Event;

/**
 * Class Csrf
 *
 * Cross Site Request Forgery Middleware
 *
 *  @package Neutrino\Http\Middleware
 */
class Csrf extends Controller implements BeforeInterface
{
    /**
     * Called before the execution of handler
     *
     * @param \Phalcon\Events\Event     $event
     * @param \Phalcon\Dispatcher|mixed $source
     * @param mixed|null                $data
     *
     * @throws \Exception
     * @return bool
     */
    public function before(Event $event, $source, $data = null)
    {
        $security     = $this->security;
        $request      = $this->request;
        $tokenChecked = false;

        // Prevent unsetted token session
        if (!$security->getSessionToken()) {
            $security->getToken();
            $security->getTokenKey();
        }

        if ($request->isAjax()) {
            $tokenChecked = $security->checkToken(
                '_csrf_token',
                $request->getHeader('X_CSRF_TOKEN')
            );
        } elseif ($request->isPost() || $request->isPut()) {
            $tokenChecked = $security->checkToken('_csrf_token');
        } elseif ($request->isGet() || $request->isDelete()) {
            $tokenChecked = $security->checkToken(
                '_csrf_token',
                $request->getQuery('_csrf_token')
            );
        }

        if (!$tokenChecked) {
            throw new TokenMismatchException;
        }

        return true;
    }
}
