<?php

namespace Test\Http;

use Luxury\Http\Client;
use Phalcon\Http\Client\Response;
use Phalcon\Http\Request\Method;
use Phalcon\Http\Response\StatusCode;
use Test\TestCase\TestCase;

/**
 * Class ClientTest
 *
 * @package     Http
 */
class ClientTest extends TestCase
{

    public function data()
    {
        $methods = [
            Method::HEAD,
            Method::GET,
            Method::POST,
            Method::PUT,
            Method::PATCH,
            Method::DELETE
        ];

        $statuses = [
            [200, '', true, true, false, false, false],
            [201, '', true, true, false, false, false],
            [301, '', false, false, true, false, false],
            [307, '', false, false, true, false, false],
            [308, '', false, false, true, false, false],
            [400, '', false, false, false, true, false],
            [404, '', false, false, false, true, false],
            [410, '', false, false, false, true, false],
            [500, '', false, false, false, false, true],
            [503, '', false, false, false, false, true],
        ];

        $call = [];

        foreach ($methods as $method) {
            foreach ($statuses as $status) {
                $call[$method . '.' . $status[0]] = array_merge([$method], $status);
            }
        }

        return $call;
    }

    /**
     * @dataProvider data
     *
     * @param $method
     * @param $status
     * @param $body
     * @param $ok
     * @param $success
     * @param $redirect
     * @param $fail
     * @param $error
     */
    public function testClient($method, $status, $body, $ok, $success, $redirect, $fail, $error)
    {
        $provider         = $this->getRequestProvider();
        $provider->status = $status;
        $provider->body   = $body;

        $client = new Client($provider);

        $response = $client->{strtolower($method)}('/');

        $this->assertEquals($body, $response->body);
        $this->assertEquals($status, $response->header->statusCode);
        $this->assertEquals($ok, $client->isOk($response));
        $this->assertEquals($success, $client->isSuccess($response));
        $this->assertEquals($redirect, $client->isRedirect($response));
        $this->assertEquals($fail, $client->isFail($response));
        $this->assertEquals($error, $client->isError($response));
    }

    public function testAutoRedirect()
    {
        $provider           = $this->getRequestProvider();
        $provider->status   = StatusCode::MOVED_PERMANENTLY;
        $provider->body     = '';
        $provider->location = '/';

        $client = new Client($provider);

        $response = $client->get('/', [], [], true);
        $this->assertEquals('', $response->body);
        $this->assertEquals(200, $response->header->statusCode);
        $this->assertEquals(true, $client->isOk($response));
        $this->assertEquals(true, $client->isSuccess($response));
        $this->assertEquals(false, $client->isRedirect($response));
        $this->assertEquals(false, $client->isFail($response));
        $this->assertEquals(false, $client->isError($response));
        $this->assertEquals('/', $response->header->get('X-Uri'));
    }

    public function testAutoRedirectCallbackLocation()
    {
        $provider           = $this->getRequestProvider();
        $provider->status   = StatusCode::MOVED_PERMANENTLY;
        $provider->body     = '';
        $provider->location = '/';

        $client = new Client($provider);

        $response = $client->get('/', [], [], function ($location) {
            return $location . 'callbacked';
        });

        $this->assertEquals('', $response->body);
        $this->assertEquals(200, $response->header->statusCode);
        $this->assertEquals(true, $client->isOk($response));
        $this->assertEquals(true, $client->isSuccess($response));
        $this->assertEquals(false, $client->isRedirect($response));
        $this->assertEquals(false, $client->isFail($response));
        $this->assertEquals(false, $client->isError($response));
        $this->assertEquals('/callbacked', $response->header->get('X-Uri'));
    }

    public function testUnImplementedMethod()
    {
        $this->markTestSkipped("Unreachable case for now.");

        $this->setExpectedExceptionRegExp(\BadMethodCallException::class,
            '/Http Method ".+" not implemented\./');

        $client = new Client();

        $client->weirdHttpMethod(' weird http method, wierd url ');
    }

    /**
     * @return RequestProviderStub
     */
    private function getRequestProvider()
    {
        return new RequestProviderStub;
    }
}

class RequestProviderStub
{
    public $location;
    public $status;
    public $body;

    private function makeResponse($uri)
    {
        $response = new Response();

        $response->body               = $this->body;
        $response->header->statusCode = $this->status;
        $response->header->set('X-Uri', $uri);

        if (isset($this->location)) {
            $response->header->set('Location', $this->location);

            $this->location = null;
            $this->status   = 200;
        }

        return $response;
    }

    public function get($uri)
    {
        return $this->makeResponse($uri);
    }

    public function head($uri)
    {
        return $this->makeResponse($uri);
    }

    public function post($uri)
    {
        return $this->makeResponse($uri);
    }

    public function put($uri)
    {
        return $this->makeResponse($uri);
    }

    public function patch($uri)
    {
        return $this->makeResponse($uri);
    }

    public function delete($uri)
    {
        return $this->makeResponse($uri);
    }
}
