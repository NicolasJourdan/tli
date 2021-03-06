<?php

namespace App\Auth;

use App\Repository\UserRepository;

class UserManager
{
    const UNIQUE_KEY = 'email';

    /** @var UserRepository */
    private $userRepository;

    /** @var Hasher */
    private $hasher;

    public function __construct(UserRepository $userRepository, Hasher $hasher)
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }

    /**
     * @param array $userParams
     *
     * @return bool
     */
    public function register(array $userParams): bool
    {
        $userParams = $this->formatData($userParams);

        if (!$userParams) {
            return false;
        }

        $user = $this->userRepository->getUserByEmail($userParams['email']);

        return $user ? false : $this->userRepository->insert($userParams);
    }

    /**
     * @param array $userParams
     *
     * @return bool
     */
    public function update(array $userParams): bool
    {
        return !$userParams ? false : $this->userRepository->update($userParams, self::UNIQUE_KEY);
    }

    /**
     * @param array $userParams
     *
     * @return bool
     */
    public function updatePassword(array $userParams): bool
    {
        $userParams = $this->formatData($userParams);

        return !$userParams ? false : $this->userRepository->update($userParams, self::UNIQUE_KEY);
    }

    /**
     * @param array $userParams
     *
     * @return bool
     *
     * @throws \ReflectionException
     */
    public function delete(array $userParams): bool
    {
        // Test if the user exists
        if (!$this->userRepository->getByRows($userParams)) {
            return false;
        }

        return $this->userRepository->delete($userParams);
    }

    /**
     * @param array $userParams
     *
     * @return array
     */
    private function formatData(array $userParams): ?array
    {
        // Test if the passwords are identical
        if (!($userParams['password'] === $userParams['password_confirm'])) {
            return null;
        }

        // Remove the confirmed password from the user params
        unset($userParams['password_confirm']);

        $userParams['password'] = $this->hasher->hash($userParams['password']);

        return $userParams;
    }
}
