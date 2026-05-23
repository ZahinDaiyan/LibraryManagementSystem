<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Middleware\AdminMiddleware;

class DashboardController extends Controller
{
    protected AdminMiddleware $middleware;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->middleware = new AdminMiddleware();
    }

    public function index(): void
    {
        if (!$this->middleware->handle($this->request, $this->response)) {
            return;
        }

        $this->view('Admin.dashboard');
    }
}
