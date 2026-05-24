<?php

namespace App\Controllers\Auth;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

class LoginController extends Controller
{
    protected AuthService $authService;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->authService = new AuthService();
    }

    public function showLogin(): void
    {
        $this->view('Login', [
            'error' => $_SESSION['error'] ?? null,
        ]);
    }

    public function authenticate(): void
    {
        $email = trim($this->request->input('email', ''));
        $password = trim($this->request->input('password', ''));

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Please fill all fields';
            $this->redirect('/login');
            return;
        }

        $user = $this->authService->attempt([
            'email' => $email,
            'password' => $password,
        ]);

        if ($user) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'] === 'manager' ? 'branch_manager' : $user['role'];
            unset($_SESSION['error'], $_SESSION['msg']);
            $this->redirect('/');
        }

        $_SESSION['error'] = 'Invalid Credentials';
        $this->redirect('/login');
    }

    public function logout(): void
    {
        session_destroy();
        session_start();
        $this->redirect('/');
    }
}
