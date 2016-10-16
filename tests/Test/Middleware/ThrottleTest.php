<?php
namespace Test\Middleware;

use Luxury\Constants\Services;
use Phalcon\Http\Response;
use Phalcon\Http\Response\StatusCode;
use Test\TestCase\TestCase;
use Test\TestCase\UseCaches;

/**
 * Trait ThrottleTest
 *
 * @package Middleware
 */
class ThrottleTest extends TestCase
{
    use UseCaches;

    public function testThrottle()
    {
        $this->app->useImplicitView(false);

        $this->app->router->addGet('/', [
            'namespace'  => 'Test\Stub',
            'controller' => 'Stubthrottled',
            'action'     => 'index'
        ]);

        $msg    = StatusCode::message(StatusCode::TOO_MANY_REQUESTS);
        $status = StatusCode::TOO_MANY_REQUESTS . ' ' . $msg;
        for ($i = 0; $i < 11; $i++) {
            // WHEN
            $this->app->handle('/');
            $response = $this->app->response;
            if ($i < 10) {
                $this->assertNotEquals($status, $response->getStatusCode());
                $this->assertNotEquals($msg, $response->getContent());
                $this->assertEquals(10, $response->getHeaders()->get('X-RateLimit-Limit'));
                $this->assertEquals(9 - $i, $response->getHeaders()->get('X-RateLimit-Remaining'));
                $this->assertEquals(null, $response->getHeaders()->get('Retry-After'));
            } else {
                $this->assertEquals($status, $response->getStatusCode());
                $this->assertEquals($msg, $response->getContent());
                $this->assertEquals(10, $response->getHeaders()->get('X-RateLimit-Limit'));
                $this->assertEquals(0, $response->getHeaders()->get('X-RateLimit-Remaining'));
                $this->assertEquals(60, $response->getHeaders()->get('Retry-After'));
            }
        }

        usleep(1000000);
        $this->app->handle('/');

        $response = $this->app->response;

        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($msg, $response->getContent());
        $this->assertEquals(10, $response->getHeaders()->get('X-RateLimit-Limit'));
        $this->assertEquals(0, $response->getHeaders()->get('X-RateLimit-Remaining'));
        $this->assertEquals(59, $response->getHeaders()->get('Retry-After'));
    }

    public function testThrottleFiltered()
    {
        $this->app->useImplicitView(false);

        $this->app->router->addGet('/', [
            'namespace'  => 'Test\Stub',
            'controller' => 'Stubthrottled',
            'action'     => 'index'
        ]);

        $this->app->router->addGet('/throttled', [
            'namespace'  => 'Test\Stub',
            'controller' => 'Stubthrottled',
            'action'     => 'throttled'
        ]);

        $msg    = StatusCode::message(StatusCode::TOO_MANY_REQUESTS);
        $status = StatusCode::TOO_MANY_REQUESTS . ' ' . $msg;
        for ($i = 0; $i < 10; $i++) {
            // WHEN
            $this->app->handle('/');
            $response = $this->app->getDI()->getShared(Services::RESPONSE);

            $this->assertNotEquals($status, $response->getStatusCode());
            $this->assertNotEquals($msg, $response->getContent());
            $this->assertEquals(10, $response->getHeaders()->get('X-RateLimit-Limit'));
            $this->assertEquals(9 - $i, $response->getHeaders()->get('X-RateLimit-Remaining'));
            $this->assertEquals(null, $response->getHeaders()->get('Retry-After'));
        }

        usleep(1000000);

        $this->app->getDI()->remove(Services::RESPONSE);
        $this->app->getDI()->setShared(Services::RESPONSE, function () {
            $response = new Response();
            $response->setHeaders(new Response\Headers());

            return $response;
        });

        $this->app->handle('/throttled');

        $response = $this->app->getDI()->getShared(Services::RESPONSE);

        $this->assertEquals('200 OK', $response->getStatusCode());
        $this->assertEquals('', $response->getContent());
        $this->assertEquals(false, $response->getHeaders()->get('X-RateLimit-Limit'));
        $this->assertEquals(false, $response->getHeaders()->get('X-RateLimit-Remaining'));
        $this->assertEquals(false, $response->getHeaders()->get('Retry-After'));

        $this->app->getDI()->remove(Services::RESPONSE);
        $this->app->getDI()->setShared(Services::RESPONSE, function () {
            $response = new Response();
            $response->setHeaders(new Response\Headers());

            return $response;
        });

        $this->app->handle('/');

        $response = $this->app->getDI()->getShared(Services::RESPONSE);

        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($msg, $response->getContent());
        $this->assertEquals(10, $response->getHeaders()->get('X-RateLimit-Limit'));
        $this->assertEquals(0, $response->getHeaders()->get('X-RateLimit-Remaining'));
        $this->assertEquals(60, $response->getHeaders()->get('Retry-After'));
    }
}
