<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class HomeController extends Controller
{
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
    }

    public function index(): void
    {
        if (empty($_SESSION['id'])) {
            $this->redirect('/login');
            return;
        }

        $role = $_SESSION['role'] ?? '';
        switch ($role) {
            case 'admin':
                $this->redirect('/admin');
                break;
            case 'branch_manager':
                $this->redirect('/Controllers/BranchManagerDashboardController.php');
                break;
            case 'librarian':
                $this->redirect('/Controllers/LibrarianDashboardController.php');
                break;
            case 'member':
                $this->redirect('/Controllers/MemberDashboardController.php');
                break;
            default:
                $this->redirect('/login');
                break;
        }
    }
}
