<?php

namespace Neutrino\Test;

use Neutrino\Constants\Services;

/**
 * Class FuncTestCase
 *
 *  @package Neutrino\Test
 */
abstract class FuncTestCase extends TestCase
{

    /**
     * @param string $service
     * @param string $class
     * @param bool   $shared
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function mockService($service, $class, $shared)
    {
        if ($this->getDI()->has($service)) {
            $this->getDI()->remove($service);
        }

        $instance = $this->createMock($class);

        $this->getDI()->set($service, $instance, $shared);

        return $instance;
    }

    /**
     * Assert that the last dispatched controller matches the given controller class name
     *
     * @param  string $expected The expected controller name
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function assertController($expected)
    {
        $actual = $this->getDI()->getShared(Services::DISPATCHER)->getControllerName();
        if ($actual != $expected) {
            throw new \PHPUnit_Framework_ExpectationFailedException(
                sprintf(
                    'Failed asserting Controller name "%s", actual Controller name is "%s"',
                    $expected,
                    $actual
                )
            );
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * Assert that the last dispatched action matches the given action name
     *
     * @param  string $expected The expected action name
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function assertAction($expected)
    {
        $actual = $this->getDI()->getShared(Services::DISPATCHER)->getActionName();
        if ($actual != $expected) {
            throw new \PHPUnit_Framework_ExpectationFailedException(
                sprintf(
                    'Failed asserting Action name "%s", actual Action name is "%s"',
                    $expected,
                    $actual
                )
            );
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * Assert that the response headers contains the given array
     * <code>
     * $expected = array('Content-Type' => 'application/json')
     * </code>
     *
     * @param  array $expected The expected headers
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function assertHeader(array $expected)
    {
        foreach ($expected as $expectedField => $expectedValue) {
            $actualValue =
                $this->getDI()->getShared(Services::RESPONSE)->getHeaders()->get($expectedField);
            if ($actualValue != $expectedValue) {
                throw new \PHPUnit_Framework_ExpectationFailedException(
                    sprintf(
                        'Failed asserting "%s" has a value of "%s", actual "%s" header value is "%s"',
                        $expectedField,
                        $expectedValue,
                        $expectedField,
                        $actualValue
                    )
                );
            }
            $this->assertEquals($expectedValue, $actualValue);
        }
    }

    /**
     * Asserts that the response code matches the given one
     *
     * @param  string $expected the expected response code
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function assertResponseCode($expected)
    {
        // convert to string if int
        if (is_integer($expected)) {
            $expected = (string)$expected;
        }

        $actualValue = $this->getDI()->getShared(Services::RESPONSE)->getHeaders()->get('Status');

        if (empty($actualValue) || stristr($actualValue, $expected) === false) {
            throw new \PHPUnit_Framework_ExpectationFailedException(
                sprintf(
                    'Failed asserting response code is "%s", actual response status is "%s"',
                    $expected,
                    $actualValue
                )
            );
        }

        $this->assertContains($expected, $actualValue);
    }

    /**
     * Asserts that the dispatch is forwarded
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function assertDispatchIsForwarded()
    {
        /* @var $dispatcher \Phalcon\Mvc\Dispatcher */
        $dispatcher = $this->getDI()->getShared(Services::DISPATCHER);
        $actual = $dispatcher->wasForwarded();

        if (!$actual) {
            throw new \PHPUnit_Framework_ExpectationFailedException(
                'Failed asserting dispatch was forwarded'
            );
        }

        $this->assertTrue($actual);
    }

    /**
     * Assert location redirect
     *
     * @param  string $location
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function assertRedirectTo($location)
    {
        $actualLocation =
            $this->getDI()->getShared(Services::RESPONSE)->getHeaders()->get('Location');

        if (!$actualLocation) {
            throw new \PHPUnit_Framework_ExpectationFailedException(
                'Failed asserting response caused a redirect'
            );
        }

        if ($actualLocation !== $location) {
            throw new \PHPUnit_Framework_ExpectationFailedException(
                sprintf(
                    'Failed asserting response redirects to "%s". It redirects to "%s".',
                    $location,
                    $actualLocation
                )
            );
        }

        $this->assertEquals($location, $actualLocation);
    }

    /**
     * Convenience method to retrieve response content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getDI()->getShared(Services::RESPONSE)->getContent();
    }

    /**
     * Assert response content contains string
     *
     * @param string $string
     */
    public function assertResponseContentContains($string)
    {
        $this->assertContains($string, $this->getContent());
    }

    /**
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Ensures that each test has it's own DI and all globals are purged
     *
     * @return void
     */
    protected function tearDown()
    {
        $_SESSION = [];
        $_GET = [];
        $_POST = [];
        $_COOKIE = [];
        $_REQUEST = [];
        $_FILES = [];
        parent::tearDown();
    }

    /**
     * Dispatches a given url and sets the response object accordingly
     *
     * @param string $url    request url
     * @param string $method request method
     * @param array  $params request params
     * @param mixed  &$output
     *
     * @throws \Error|\Exception
     */
    protected function dispatch($url, $method = 'GET', $params = [], &$output = '')
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        foreach ($params as $key => $param) {
            switch ($method) {
                case 'GET':
                case 'PATCH':
                    $_GET[$key] = $param;
                    break;
                case 'POST':
                case 'PUT':
                    $_POST[$key] = $param;
            }
        }

        ob_start();
        try {
            $this->getDI()->setShared(Services::RESPONSE, $this->app->handle($url));
        } catch(\Exception $e) {
        } catch(\Error $e) {
        }

        if(isset($e)){
            ob_end_clean();

            throw $e;
        }

        $output = ob_get_clean();


        foreach ($params as $key => $param) {
            switch ($method) {
                case 'GET':
                case 'PATCH':
                    unset($_GET[$key]);
                    break;
                case 'POST':
                case 'PUT':
                    unset($_POST[$key]);
            }
        }
    }

    /**
     * Dispatches a given command line
     *
     * @param string $cli
     */
    protected function dispatchCli($cli)
    {
        $this->app->setArgument(explode(' ', $cli));

        $this->app->handle();
    }
}
