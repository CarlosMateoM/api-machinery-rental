<?php

namespace dao;

require_once './model/User.php';

use \model\User;

interface AuthDao
{
    public function login(User $user): ?User;
    public function getUserByEmail(string $email): ?User;
    public function setResetToken($userId, $token): void;
    public function updatePassword($userId, $hashedPassword): void;
    public function removeResetToken($userId): void;
}