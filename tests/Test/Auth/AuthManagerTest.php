<?php
/**
 * Created by PhpStorm.
 * User: xlzi590
 * Date: 18/10/2016
 * Time: 10:45
 */

namespace Test\Auth;


use Neutrino\Auth\Manager as AuthManager;
use Neutrino\Constants\Services;
use Neutrino\Foundation\Auth\User;
use Neutrino\Support\Facades\Auth;
use Phalcon\Db\Column;
use Phalcon\Http\Response\Cookies;
use Phalcon\Security;
use Test\TestCase\TestCase;

class AuthManagerTest extends TestCase
{
    public function setUp()
    {
        global $config;

        $config['session']['id'] = 'unittest';
        $config['auth']['model'] = BasicUser::class;

        parent::setUp();
    }

    public function mockDb($numRows, $fetchall)
    {
        $con = $this->mockService(Services::DB, \Phalcon\Db\Adapter\Pdo\Mysql::class, true);

        $dialect = $this->createMock(\Phalcon\Db\Dialect\Mysql::class);

        $results = $this->createMock(\Phalcon\Db\Result\Pdo::class);

        $results->expects($this->any())
            ->method('numRows')
            ->will($this->returnValue($numRows));

        $results->expects($this->any())
            ->method('fetchall')
            ->will($this->returnValue($fetchall));

        $dialect->expects($this->any())
            ->method('select')
            ->will($this->returnValue(null));

        $con->expects($this->any())
            ->method('getDialect')
            ->will($this->returnValue($dialect));

        $con->expects($this->any())
            ->method('query')
            ->will($this->returnValue($results));

        $con->expects($this->any())
            ->method('execute');

        $con->expects($this->any())
            ->method('tableExists')
            ->will($this->returnValue(true));
    }

    public function testNoAttemps()
    {
        $this->mockDb(0, null);
        /** @var AuthManager $authManager */
        $authManager = new AuthManager();

        $this->assertNull($authManager->attempt([
            'email'    => '',
            'password' => ''
        ]));

        $this->assertFalse($authManager->check());
        $this->assertTrue($authManager->guest());
    }

    public function testAttemps()
    {
        $security = $this->getDI()->getShared(Services::SECURITY);

        $this->mockDb(1, [
            [
                'id'       => 1,
                'email'    => 'test@email.com',
                'password' => $security->hash('1a2b3c4d5e')
            ]
        ]);
        /** @var AuthManager $authManager */
        $authManager = new AuthManager();
        $this->getDI()->setShared(Services::AUTH, $authManager);

        $sessionService = $this->mockService(Services::SESSION, \Phalcon\Session\Adapter\Files::class, true);

        $sessionService->expects($this->once())->method('regenerateId')->willReturn(1);
        $sessionService->expects($this->once())->method('set')->with('unittest', 'test@email.com');

        /** @var User $user */
        $user = Auth::attempt([
            'email'    => 'test@email.com',
            'password' => '1a2b3c4d5e'
        ]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user, Auth::user());

        $this->assertTrue(Auth::check());
        $this->assertFalse(Auth::guest());
        $this->assertEquals('test@email.com', $user->getAuthIdentifier());
        $this->assertTrue($security->checkHash('1a2b3c4d5e', $user->getAuthPassword()));
    }

    public function testAttempsFail()
    {
        $security = $this->getDI()->getShared(Services::SECURITY);

        $this->mockDb(1, [
            [
                'id'       => 1,
                'email'    => 'test@email.com',
                'password' => $security->hash('1a2b3c4d5e')
            ]
        ]);
        /** @var AuthManager $authManager */
        $authManager = new AuthManager();
        $this->getDI()->setShared(Services::AUTH, $authManager);

        /** @var User $user */
        $user = Auth::attempt([
            'email'    => 'test@email.com',
            'password' => '1a2b3c4ddadaaddad5e'
        ]);
        $this->assertNull($user);
        $this->assertNull(Auth::user());

        $this->assertFalse(Auth::check());
        $this->assertTrue(Auth::guest());
    }

    public function testAttempsCustomModel()
    {
        $this->getDI()->getShared(Services::CONFIG)->auth->model = CustomUser::class;

        $security = $this->getDI()->getShared(Services::SECURITY);

        $this->mockDb(1, [
            [
                'id'               => 1,
                'my_user_name'     => 'test@email.com',
                'my_user_password' => $security->hash('1a2b3c4d5e')
            ]
        ]);
        /** @var AuthManager $authManager */
        $authManager = new AuthManager();
        $this->getDI()->setShared(Services::AUTH, $authManager);

        $sessionService = $this->mockService(Services::SESSION, \Phalcon\Session\Adapter\Files::class, true);

        $sessionService->expects($this->once())->method('regenerateId')->willReturn(1);
        $sessionService->expects($this->once())->method('set')->with('unittest', 'test@email.com');

        /** @var CustomUser $user */
        $user = Auth::attempt([
            'my_user_name'     => '',
            'my_user_password' => '1a2b3c4d5e'
        ]);
        $this->assertInstanceOf(CustomUser::class, $user);
        $this->assertEquals($user, Auth::user());

        $this->assertTrue(Auth::check());
        $this->assertFalse(Auth::guest());
        $this->assertEquals('test@email.com', $user->getAuthIdentifier());
        $this->assertTrue($security->checkHash('1a2b3c4d5e', $user->getAuthPassword()));
    }

    public function testAttempsWithRemember()
    {
        $security = $this->getDI()->getShared(Services::SECURITY);

        $this->mockDb(1, [
            [
                'id'       => 1,
                'email'    => 'test@email.com',
                'password' => $security->hash('1a2b3c4d5e')
            ]
        ]);
        /** @var AuthManager $authManager */
        $authManager = new AuthManager();
        $this->getDI()->setShared(Services::AUTH, $authManager);

        $sessionService = $this->mockService(Services::SESSION, \Phalcon\Session\Adapter\Files::class, true);

        $sessionService->expects($this->once())->method('regenerateId')->willReturn(1);
        $sessionService->expects($this->once())->method('set')->with('unittest', 'test@email.com');

        /** @var User $user */
        $user = Auth::attempt([
            'email'    => 'test@email.com',
            'password' => '1a2b3c4d5e'
        ], true);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user, Auth::user());

        $this->assertTrue(Auth::check());
        $this->assertFalse(Auth::guest());
        $this->assertEquals('test@email.com', $user->getAuthIdentifier());
        $this->assertTrue($security->checkHash('1a2b3c4d5e', $user->getAuthPassword()));

        /** @var Cookies $cookies */
        $cookies = $this->getDI()->getShared(Services::COOKIES);
        $this->assertTrue($cookies->has('remember_me'));

        $cookieValue = $cookies->get('remember_me')->getValue();
        $this->assertCount(2, explode('|', $cookieValue));
        $this->assertEquals('test@email.com', explode('|', $cookieValue)[0]);
    }

    public function testAttempsViaRemember()
    {
        /** @var Cookies $cookies */
        $cookies = $this->getDI()->getShared(Services::COOKIES);
        /** @var Security $security */
        $security = $this->getDI()->getShared(Services::SECURITY);
        $token = str_random(60);

        $this->mockDb(1, [
            [
                'id'             => 1,
                'email'          => 'test@email.com',
                'password'       => $security->hash('1a2b3c4d5e'),
                'remember_token' => $token
            ]
        ]);
        $cookies->set('remember_me', 'test@email.com|' . $token);

        /** @var AuthManager $authManager */
        $authManager = new AuthManager();
        $this->getDI()->setShared(Services::AUTH, $authManager);

        $sessionService = $this->mockService(Services::SESSION, \Phalcon\Session\Adapter\Files::class, true);

        $sessionService->expects($this->once())->method('get')->with('unittest')->willReturn(null);
        $sessionService->expects($this->once())->method('set');

        /** @var User $user */
        $user = Auth::user();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user, Auth::user());

        $this->assertTrue(Auth::check());
        $this->assertFalse(Auth::guest());
        $this->assertEquals('test@email.com', $user->getAuthIdentifier());
        $this->assertTrue($security->checkHash('1a2b3c4d5e', $user->getAuthPassword()));
    }

    public function testAttempsViaSession()
    {
        /** @var Security $security */
        $security = $this->getDI()->getShared(Services::SECURITY);
        $this->mockDb(1, [
            [
                'id'       => 1,
                'email'    => 'test@email.com',
                'password' => $security->hash('1a2b3c4d5e')
            ]
        ]);

        /** @var AuthManager $authManager */
        $authManager = new AuthManager();
        $this->getDI()->setShared(Services::AUTH, $authManager);

        $sessionService = $this->mockService(Services::SESSION, \Phalcon\Session\Adapter\Files::class, true);

        $sessionService->expects($this->once())->method('get')->with('unittest')->willReturn('test@email.com');

        /** @var User $user */
        $user = Auth::user();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user, Auth::user());

        $this->assertTrue(Auth::check());
        $this->assertFalse(Auth::guest());
        $this->assertEquals('test@email.com', $user->getAuthIdentifier());
        $this->assertTrue($security->checkHash('1a2b3c4d5e', $user->getAuthPassword()));
    }

    public function testLogout()
    {

        /** @var AuthManager $authManager */
        $authManager = new AuthManager();
        $this->getDI()->setShared(Services::AUTH, $authManager);

        $sessionService = $this->mockService(Services::SESSION, \Phalcon\Session\Adapter\Files::class, true);

        $sessionService->expects($this->once())->method('destroy');

        Auth::logout();

        $this->assertNull(Auth::user());
        $this->assertFalse(Auth::check());
        $this->assertTrue(Auth::guest());
    }
}

class BasicUser extends User
{
    public function initialize()
    {
        parent::initialize();

        $this->primary('id', Column::TYPE_INTEGER);
        $this->column('name', Column::TYPE_VARCHAR);
        $this->column('email', Column::TYPE_VARCHAR);
        $this->column('password', Column::TYPE_VARCHAR);
        $this->column('remember_token', Column::TYPE_VARCHAR);
    }
}

class CustomUser extends User
{
    public function initialize()
    {
        parent::initialize();

        $this->primary('id', Column::TYPE_INTEGER);
        $this->column('my_user_name', Column::TYPE_VARCHAR);
        $this->column('my_user_password', Column::TYPE_VARCHAR);
        $this->column('my_user_remember', Column::TYPE_VARCHAR);
    }

    public static function getAuthIdentifierName()
    {
        return 'my_user_name';
    }

    public static function getAuthPasswordName()
    {
        return 'my_user_password';
    }

    public static function getRememberTokenName()
    {
        return 'my_user_remember';
    }
}