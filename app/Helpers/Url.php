<?php

namespace App\Helpers;

class Url
{
    public static function basePath(): string
    {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if ($dir === '.' || $dir === '/') {
            return '';
        }

        return $dir;
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
