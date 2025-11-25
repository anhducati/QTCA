<?php

namespace App\Services;

use App\Services\Interfaces\UserServiceInterface;
use App\Repositories\Interfaces\UserInterface as UserRepository;

/**
 * Class UserService
 * @package App\Services
 */
class UserService implements UserServiceInterface
{
    protected $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function all()
    {
        return $this->userRepository->all();
    }
}
