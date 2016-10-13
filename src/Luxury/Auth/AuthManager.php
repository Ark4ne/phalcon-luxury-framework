<?php

namespace Luxury\Auth;

use Luxury\Support\Arr;
use Phalcon\Di\Injectable as Injector;

/**
 * Class Auth
 *
 * @package Luxury\Auth
 *
 * @property \Phalcon\Config|\stdClass config
 */
class AuthManager extends Injector
{
    /**
     * User Authenticable
     *
     * @var Authenticable
     */
    protected $user;

    /**
     * Authorization Gate
     *
     * @var Authorize
     */
    private $adapter;

    /**
     * Indicates if the logout method has been called.
     *
     * @var bool
     */
    protected $loggedOut = false;

    /**
     * Auth constructor.
     *
     * @param \Luxury\Auth\Authorize $adapter
     */
    public function __construct(\Luxury\Auth\Authorize $adapter = null)
    {
        $this->adapter = $adapter;
    }

    /**
     * Return the user authenticated
     *
     * @return \Luxury\Auth\Authenticable|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return null;
        }

        if (!is_null($this->user)) {
            return $this->user;
        }

        $id = $this->retrieveId();

        if (is_null($id)) {
            return null;
        }

        $this->user = $this->retrieveUserById($id);

        return $this->user;
    }

    /**
     * If user is NOT logged into the system return true else false;
     *
     * @return bool Guest is true, Loggedin is false
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * authenticate user
     *
     * @param  array $credentials
     *
     * @return \Phalcon\Mvc\Model\User|static
     */
    public function attempt(array $credentials = [])
    {
        $user = User::findFirst(
            [
                'email'    => Arr::get($credentials, 'email'),
                'password' => Arr::get($credentials, 'password'),
            ]
        );

        if (!is_null($user)) {
            $this->login($user);

            return $user;
        }

        return null;
    }

    /**
     * Determine if user is authenticated
     *
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Log out of the application
     */
    public function logout()
    {
        $this->user      = null;
        $this->loggedOut = true;

        $this->session->destroy();
    }

    /**
     * Get currently logged user's id
     *
     * @return mixed|null
     */
    public function retrieveId()
    {
        return $this->session->get($this->sessionKey());
    }

    /**
     * Log a user into the application
     *
     * @param $user
     *
     * @return bool
     */
    public function login($user)
    {
        if (!$user) {
            return false;
        }

        $this->regenerateSessionId();

        $this->session->set($this->sessionKey(), $user->id);

        return true;
    }

    /**
     * Log a user into the application using id
     *
     * @param int $id
     *
     * @return User
     */
    public function loginUsingId($id)
    {
        $this->login($user = $this->retrieveUserById($id));

        return $user;
    }

    /**
     * @param int $id
     *
     * @return Authenticable
     */
    protected function retrieveUserById($id)
    {
        return User::find(['id' => $id]);
    }

    /**
     * Regenerate Session ID
     */
    protected function regenerateSessionId()
    {
        $this->session->regenerateId();
    }

    /**
     * Retrieve session id
     *
     * @return mixed
     */
    private function sessionKey()
    {
        return $this->config->session->id;
    }
}
