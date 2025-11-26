<?php

namespace App\Repositories\Interfaces;

interface UserInterface
{
    public function all();

    public function getUserById(int $id);

    public function getUserByToken($token);

    public function getUserByEmail($email);

    public function getAllUser();

    public function getRecordUser();
}
