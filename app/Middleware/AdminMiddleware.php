<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class AdminMiddleware extends AuthMiddleware
{
    public function handle(Request $request, Response $response): bool
    {
        if (!parent::handle($request, $response)) {
            return false;
        }

        if (($_SESSION['role'] ?? '') !== 'admin') {
            $response->setStatusCode(403);
            echo 'Forbidden';
            return false;
        }

        return true;
    }
}
