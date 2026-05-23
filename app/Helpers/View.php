<?php

namespace App\Helpers;

class View
{
    public static function render(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewName = preg_replace('/View$/', '', $name);
        $viewFile = __DIR__ . '/../../Views/' . str_replace('.', '/', $viewName) . 'View.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException('View not found: ' . $viewFile);
        }

        require $viewFile;
    }
}
