<?php

namespace Test\Http;

use Fake\Kernels\Http\Controllers\StubController;
use Neutrino\Constants\Services;
use Neutrino\Exceptions\TokenMismatchException;
use Neutrino\Http\Middleware\Csrf;
use Neutrino\Http\Standards\StatusCode;
use Phalcon\Security;
use Phalcon\Session\Adapter;
use Phalcon\Version;
use Test\TestCase\TestCase;

class CsrfTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        StubController::$middlewares[] = [
            'middleware' => Csrf::class,
            'params'     => [
                'only' => ['index']
            ]
        ];

        $mock = $this->mockService(Services::SESSION, Adapter\Files::class, true);

        $mock->expects($this->any())->method('start')->willReturn(true);
        $mock->expects($this->any())->method('isStarted')->willReturn(true);
        $mock->expects($this->any())->method('regenerateId')->willReturnSelf();
        $mock->expects($this->any())->method('setOptions');
        $mock->expects($this->any())->method('setName');
        $mock->expects($this->any())->method('getName')->willReturn("PHPUNIT_NEUTRINO_SESSION");
    }

    public function tearDown()
    {
        parent::tearDown();

        StubController::$middlewares = [];
        StubController::$registerMiddlewares = [];
    }

    public function assertResponseStatusCode($expected)
    {
        $msg = StatusCode::message($expected);
        if (Version::getPart(Version::VERSION_MEDIUM) >= 2) {
            $status = $expected;
        } else {
            $status = $expected . ' ' . $msg;
        }

        $this->assertEquals($status, $this->getDI()->get('response')->getStatusCode());
    }

    public function testCsrfFail_Get()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $security */
        $security = $this->mockService(Services::SECURITY, Security::class, true);

        $security->expects($this->any())->method('getSessionToken')->willReturn(true);
        $security->expects($this->any())->method('getToken')->willReturn("token");
        $security->expects($this->any())->method('getTokenKey')->willReturn("tokenKey");
        $security->expects($this->any())->method('checkToken')->willReturn(false);

        /** @var \PHPUnit_Framework_MockObject_MockObject $session */
        $session = $this->getDI()->getShared(Services::SESSION);

        $session->expects($this->any())->method('get')->willReturn(false);

        $this->expectException(TokenMismatchException::class);

        $this->dispatch('/');
    }

    public function testCsrfFail_Post()
    {
        $this->expectException(TokenMismatchException::class);

        $this->dispatch('/', 'POST');
    }

    public function testCsrfOk_Get()
    {
        /** @var \Phalcon\Security $security */
        $security = $this->getDI()->getShared(Services::SECURITY);
        /** @var \PHPUnit_Framework_MockObject_MockObject $session */
        $session = $this->getDI()->getShared(Services::SESSION);

        $session->expects($this->any())->method('get')->willReturn($security->getToken());

        $this->dispatch('/', 'GET', ['_csrf_token' => $security->getToken()]);

        $this->assertEquals(null, $this->getDI()->get('response')->getStatusCode());
    }

    public function testCsrfOk_Post()
    {
        /** @var \Phalcon\Security $security */
        $security = $this->getDI()->getShared(Services::SECURITY);
        /** @var \PHPUnit_Framework_MockObject_MockObject $session */
        $session = $this->getDI()->getShared(Services::SESSION);

        $session->expects($this->any())->method('get')->willReturn($security->getToken());

        $this->dispatch('/', 'POST', ['_csrf_token' => $security->getToken()]);

        $this->assertEquals(null, $this->getDI()->get('response')->getStatusCode());
    }

    public function testCsrfOk_Ajax()
    {
        /** @var \Phalcon\Security $security */
        $security = $this->getDI()->getShared(Services::SECURITY);
        /** @var \PHPUnit_Framework_MockObject_MockObject $session */
        $session = $this->getDI()->getShared(Services::SESSION);

        $session->expects($this->any())->method('get')->willReturn($security->getToken());

        $_SERVER["HTTP_X_REQUESTED_WITH"]              = "XMLHttpRequest";
        $_SERVER['HTTP_X_CSRF_TOKEN'] = $security->getToken();

        $this->dispatch('/', 'POST', []);

        $this->assertEquals(null, $this->getDI()->get('response')->getStatusCode());
    }

    public function testCsrfFail_Ajax()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $security */
        $security = $this->mockService(Services::SECURITY, Security::class, true);

        $security->expects($this->any())->method('getSessionToken')->willReturn(true);
        $security->expects($this->any())->method('getToken')->willReturn("token");
        $security->expects($this->any())->method('getTokenKey')->willReturn("tokenKey");
        $security->expects($this->any())->method('checkToken')->willReturn(false);

        /** @var \PHPUnit_Framework_MockObject_MockObject $session */
        $session = $this->getDI()->getShared(Services::SESSION);

        $session->expects($this->any())->method('get')->willReturn(null);

        $_SERVER["HTTP_X_REQUESTED_WITH"] = "XMLHttpRequest";

        $this->expectException(TokenMismatchException::class);

        $this->dispatch('/', 'POST', []);
    }
}
