<?php

namespace App\Auth;


use App\Entity\User;
use App\Repository\UserRepository;
use Beaver\Request\Request;

class Authenticator
{
    const SESSION_USER_KEY = 'user';

    /** @var UserRepository */
    private $userRepository;

    /** @var Hasher */
    private $hasher;

    /** @var Request */
    private $request;

    /** @var User */
    private $user;

    /**
     * Authenticator constructor.
     *
     * @param UserRepository $userRepository
     * @param Hasher $hasher
     * @param Request $request
     */
    public function __construct(UserRepository $userRepository, Hasher $hasher, Request $request)
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->request = $request;
    }

    /**
     * Connects a user given the credentials
     *
     * @param string $email
     * @param string $password
     *
     * @return bool if the user could be connected
     */
    public function connection(string $email, string $password): bool
    {
        // user already connected
        if ($this->request->getSessionValue(self::SESSION_USER_KEY)) {
            return false;
        }

        /** @var User $user */
        $user = $this->userRepository->getUserByEmail($email);
        if (!$user) {
            return false;
        }

        if (!$this->hasher->verrify($user->getPassword(), $password)) {
            return false;
        }

        // the email and password are correct, save the user in session
        $this->request->setSessionValue(self::SESSION_USER_KEY, $user->getId());
        $this->user = $user;
        return true;
    }

    /**
     * Disconnect the user
     */
    public function disconnect()
    {
        $this->request->unsetSessionValue(self::SESSION_USER_KEY);
        $this->user = null;
    }

    /**
     * Return connected user or nulls
     *
     * @return object|null
     * @throws \ReflectionException
     */
    public function getUser()
    {
        if ($this->user) {
            return $this->user;
        }

        $userId = $this->request->getSessionValue(self::SESSION_USER_KEY);
        return $userId ? $this->userRepository->getById($userId) : null;
    }
}
