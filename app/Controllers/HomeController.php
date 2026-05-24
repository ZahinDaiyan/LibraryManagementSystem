<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

require_once __DIR__ . '/../../Models/DB.php';
require_once __DIR__ . '/../../Models/BookModel.php';
require_once __DIR__ . '/../../Models/AnnouncementModel.php';

class HomeController extends Controller
{
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
    }

    public function index(): void
    {
        if (!empty($_SESSION['id'])) {
            $role = $_SESSION['role'] ?? '';
            switch ($role) {
                case 'admin':
                    $this->redirect('/admin');
                    return;
                case 'branch_manager':
                    $this->redirect('/Controllers/BranchManagerDashboardController.php');
                    return;
                case 'librarian':
                    $this->redirect('/Controllers/LibrarianDashboardController.php');
                    return;
                case 'member':
                    $this->redirect('/Controllers/MemberDashboardController.php');
                    return;
            }
        }

        $conn = Connect();
        $books = array_slice(getAdminBookCatalog($conn), 0, 6);
        $announcements = array_slice(getAllAnnouncements($conn), 0, 4);
        Close($conn);

        $this->view('Home', [
            'featuredBooks' => $books,
            'announcements' => $announcements,
        ]);
    }
}
