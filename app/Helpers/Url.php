<?php

namespace App\Helpers;

class Url
{
    public static function basePath(): string
    {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $path = trim($scriptName, '/');

        if ($path === '') {
            return '';
        }

        $segments = explode('/', $path);
        if (count($segments) <= 1) {
            return '';
        }

        return '/' . $segments[0];
    }

    public static function route(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        return self::basePath() . $path;
    }

    public static function asset(string $path): string
    {
        return self::route($path);
    }
}
