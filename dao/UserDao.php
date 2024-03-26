<?php

namespace dao;

require_once './model/User.php';

use \model\User;

interface UserDao
{
    public function createUser(User $user): ?User;
    public function readUserById(int $id): ?User;
    public function updateUser(User $user): ?User;
    public function deleteUser(int $id): ?User;
    public function allUsers(): array;
}