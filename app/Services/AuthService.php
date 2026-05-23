<?php

namespace App\Services;

class AuthService
{
    public function __construct()
    {
        require_once __DIR__ . '/../../Models/DB.php';
        require_once __DIR__ . '/../../Models/UserModel.php';
    }

    public function attempt(array $credentials): ?array
    {
        $conn = Connect();
        $user = login($conn, $credentials['email'] ?? '', $credentials['password'] ?? '');
        Close($conn);
        return $user ?: null;
    }
}
