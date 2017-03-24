<?php

namespace Neutrino\Auth;

use Neutrino\Constants\Services;
use Neutrino\Foundation\Auth\User;
use Neutrino\Interfaces\Auth\Authenticable as AuthenticableInterface;
use Phalcon\Di\Injectable;

/**
 * Class Auth
 *
 *  @package Neutrino\Auth
 */
class Manager extends Injectable
{
    /**
     * User AuthenticableInterface
     *
     * @var \Neutrino\Foundation\Auth\User
     */
    protected $user;

    /**
     * Indicates if the logout method has been called.
     *
     * @var bool
     */
    protected $loggedOut = false;

    /**
     * Model Class used. Defined in config->auth->model
     *
     * @var string
     */
    protected $model;

    /**
     * Return the user authenticated
     *
     * @return \Neutrino\Foundation\Auth\User|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return null;
        }

        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        if (!is_null($id = $this->retrieveIdentifier())) {
            $user = $this->retrieveUserByIdentifier($id);
        }

        /** @var \Phalcon\Http\Response\Cookies $cookies */
        $cookies = $this->{Services::COOKIES};
        if (empty($user) && $cookies->has('remember_me')) {
            $recaller = $cookies->get('remember_me');
            list($identifier, $token) = explode('|', $recaller);

            if ($identifier && $token) {
                $user = $this->retrieveUserByToken($identifier, $token);

                if ($user) {
                    $this->{Services::SESSION}->set($this->sessionKey(), $user->getAuthIdentifier());
                }
            }
        }

        $this->user = $user;

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
     * @param array $credentials
     * @param bool  $remember
     *
     * @return \Neutrino\Foundation\Auth\User
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $user = $this->retrieveUserByCredentials($credentials);

        if (!empty($user)) {
            $this->login($user, $remember);

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

        $this->{Services::SESSION}->destroy();
    }

    /**
     * Get currently logged user's id
     *
     * @return mixed|null
     */
    public function retrieveIdentifier()
    {
        return $this->{Services::SESSION}->get($this->sessionKey());
    }

    /**
     * Log a user into the application
     *
     * @param User $user
     * @param bool $remember
     *
     * @return bool
     */
    public function login(User $user, $remember = false)
    {
        if (!$user) {
            return false;
        }

        $this->regenerateSessionId();

        $this->{Services::SESSION}->set($this->sessionKey(), $user->getAuthIdentifier());

        if ($remember) {
            $rememberToken = str_random(60);

            /** @var \Phalcon\Http\Response\Cookies|\Phalcon\Http\Response\CookiesInterface $cookies */
            $cookies = $this->{Services::COOKIES};
            $cookies->set('remember_me', $user->getAuthIdentifier() . '|' . $rememberToken);

            $user->setRememberToken($rememberToken);

            $user->save();
        }

        $this->user = $user;

        return true;
    }

    /**
     * Log a user into the application using id
     *
     * @param int $id
     *
     * @return AuthenticableInterface|\Phalcon\Mvc\Model
     */
    public function loginUsingId($id)
    {
        $this->login($user = $this->retrieveUserById($id));

        return $user;
    }

    /**
     * Retrieve a user by his id
     *
     * @param int $id
     *
     * @return User
     */
    protected function retrieveUserById($id)
    {
        $class = $this->modelClass();

        return $class::findFirst($id);
    }

    /**
     * Retrieve a user by his identifier
     *
     * @param int $id
     *
     * @return User
     */
    protected function retrieveUserByIdentifier($id)
    {
        $class = $this->modelClass();

        return $class::findFirst([
            'conditions' => $class::getAuthIdentifierName() . ' = :auth_identifier:',
            'bind'      => [
                'auth_identifier' => $id
            ]
        ]);
    }

    /**
     * Retrieve a user by his identifier & remember token.
     *
     * @param string $identifier
     * @param string $token
     *
     * @return AuthenticableInterface|\Phalcon\Mvc\Model
     */
    protected function retrieveUserByToken($identifier, $token)
    {
        $user = $this->retrieveUserByIdentifier($identifier);

        if (!empty($user) && $user->getRememberToken() === $token) {
            return $user;
        }

        return null;
    }

    /**
     * Retrieve a user by credentials
     *
     * @param array $credentials
     *
     * @return \Neutrino\Foundation\Auth\User
     */
    protected function retrieveUserByCredentials(array $credentials)
    {
        $class = $this->modelClass();

        $identifier = $class::getAuthIdentifierName();
        $password   = $class::getAuthPasswordName();

        $user = $this->retrieveUserByIdentifier(arr_fetch($credentials, $identifier));

        if ($user) {
            /** @var \Phalcon\Security $security */
            $security = $this->{Services::SECURITY};

            if($security->checkHash(arr_fetch($credentials, $password), $user->getAuthPassword())){
                return $user;
            }
        }

        return null;
    }

    /**
     * Regenerate Session ID
     */
    protected function regenerateSessionId()
    {
        $this->{Services::SESSION}->regenerateId();
    }

    /**
     * Retrieve session id
     *
     * @return mixed
     */
    private function sessionKey()
    {
        return $this->{Services::CONFIG}->session->id;
    }

    /**
     * @return string|\Neutrino\Foundation\Auth\User
     */
    private function modelClass()
    {
        if (!isset($this->model)) {
            $this->model = '\\' . $this->{Services::CONFIG}->auth->model;
        }

        return $this->model;
    }
}
