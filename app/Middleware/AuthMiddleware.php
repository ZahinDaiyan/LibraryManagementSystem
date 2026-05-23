<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class AuthMiddleware
{
    public function handle(Request $request, Response $response): bool
    {
        if (empty($_SESSION['id'])) {
            $response->redirect('/login');
            return false;
        }

        return true;
    }
}
