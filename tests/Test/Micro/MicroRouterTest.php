<?php

namespace Test\Micro;

use Neutrino\Constants\Services;
use Neutrino\Micro\Router;
use Test\TestCase\TestCase;

class MicroRouterTest extends TestCase
{
    use MicroTestCase;

    public function dataRegisteringHttpMethod()
    {
        return [
            ['test.get', 'get', 'test.get', function () {
                /** @var \Neutrino\Foundation\Micro\Kernel $this */
                $this->response->setContent('test.get');

                return $this->response;
            }],
            ['test.post', 'post', 'test.post', function () {
                /** @var \Neutrino\Foundation\Micro\Kernel $this */
                $this->response->setContent('test.post');

                return $this->response;
            }],
            ['test.delete', 'delete', 'test.delete', function () {
                /** @var \Neutrino\Foundation\Micro\Kernel $this */
                $this->response->setContent('test.delete');

                return $this->response;
            }],
            ['test.put', 'put', 'test.put', function () {
                /** @var \Neutrino\Foundation\Micro\Kernel $this */
                $this->response->setContent('test.put');

                return $this->response;
            }],
            ['test.patch', 'patch', 'test.patch', function () {
                /** @var \Neutrino\Foundation\Micro\Kernel $this */
                $this->response->setContent('test.patch');

                return $this->response;
            }],
            ['test.options', 'options', 'test.options', function () {
                /** @var \Neutrino\Foundation\Micro\Kernel $this */
                $this->response->setContent('test.options');

                return $this->response;
            }],
            ['test.head', 'head', 'test.head', function () {
                /** @var \Neutrino\Foundation\Micro\Kernel $this */
                $this->response->setContent('test.head');

                return $this->response;
            }],
        ];
    }

    /**
     * @dataProvider dataRegisteringHttpMethod
     *
     * @param $expected
     * @param $httpMethod
     * @param $path
     * @param $handler
     */
    public function testRegisteringHttpMethod($expected, $httpMethod, $path, $handler)
    {
        /** @var Router $router */
        $router = $this->app->{Services::MICRO_ROUTER};

        $router->{'add' . ucfirst($httpMethod)}($path, $handler);

        $this->dispatch($path, strtoupper($httpMethod), [], $output);

        $this->assertEquals($output, $this->getContent());
        $this->assertEquals($expected, $this->getContent());
        $this->assertEquals($expected, $output);
    }

    public function dataTryRegisteringUnsupportedHttpMethod()
    {
        return [
            ['purge'],
            ['connect'],
            ['trace'],
        ];
    }

    /**
     * @dataProvider dataTryRegisteringUnsupportedHttpMethod
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp  /Neutrino\\Micro\\Router::add\w+: Micro Application doesn't support HTTP \w+ method\./
     *
     * @param $httpMethod
     */
    public function testTryRegisteringUnsupportedHttpMethod($httpMethod) {

        /** @var Router $router */
        $router = $this->app->{Services::MICRO_ROUTER};

        $router->{'add' . ucfirst($httpMethod)}('', function(){});
    }

    public function dataUnsupportedMethods()
    {
        return [
            ['setDefaultModule'],
            ['setDefaultController'],
            ['setDefaultAction'],
            ['setDefaults'],
            ['clear'],
            ['getModuleName'],
        ];
    }

    /**
     * @dataProvider dataUnsupportedMethods
     * @expectedException \RuntimeException
     */
    public function testUnsupportedMethods($method)
    {
        /** @var Router $router */
        $router = $this->app->{Services::MICRO_ROUTER};

        $router->$method(...[[], [], []]);
    }
}