<?php

namespace App\Helpers;

class Csrf
{
    public static function token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function verify(mixed $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($token) || !is_string($token)) {
            return false;
        }
        $stored = $_SESSION['_csrf'] ?? '';
        if (empty($stored)) {
            return false;
        }
        return hash_equals($stored, $token);
    }
}
