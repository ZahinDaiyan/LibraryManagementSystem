<?php

namespace App\Core;

use App\Helpers\Url;
use App\Helpers\View;

class Controller
{
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    protected function view(string $name, array $data = []): void
    {
        View::render($name, $data);
    }

    protected function redirect(string $path): void
    {
        if (str_starts_with($path, '/')) {
            $path = Url::route($path);
        }

        $this->response->redirect($path);
    }

    protected function json(mixed $data): void
    {
        $this->response->json($data);
    }

    protected function with(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }
}
