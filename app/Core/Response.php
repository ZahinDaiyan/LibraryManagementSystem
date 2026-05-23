<?php

namespace App\Core;

class Response
{
    public function setStatusCode(int $code): self
    {
        http_response_code($code);
        return $this;
    }

    public function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }

    public function json(mixed $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
