<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\Url;
use App\Services\UserService;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->userService = new UserService();
    }

    public function index(): void
    {
        $search = $this->request->query('search', '');
        $roleFilter = $this->request->query('role_filter', '');
        $users = $this->userService->searchUsersWithBranch($search, $roleFilter);

        $this->view('Admin.UserListView', [
            'users' => $users,
            'search' => $search,
            'role_filter' => $roleFilter,
        ]);
    }

    public function create(): void
    {
        $branches = $this->userService->listBranches();
        $this->view('Admin.UserFormView', [
            'branches' => $branches,
            'user' => null,
            'errors' => [],
            'old_data' => [],
        ]);
    }

    public function store(): void
    {
        $data = $this->request->only(['name', 'email', 'password', 'phone', 'role', 'branch_id']);
        $errors = $this->validateInput($data, false);

        if (!empty($errors)) {
            $branches = $this->userService->listBranches();
            $this->view('Admin.UserFormView', [
                'branches' => $branches,
                'errors' => $errors,
                'old_data' => $data,
                'user' => null,
            ]);
            return;
        }

        if (!$this->userService->createUser($data)) {
            $branches = $this->userService->listBranches();
            $errors['general'] = 'Unable to create user. Please try again.';
            $this->view('Admin.UserFormView', [
                'branches' => $branches,
                'errors' => $errors,
                'old_data' => $data,
                'user' => null,
            ]);
            return;
        }

        $this->redirect('/admin/users');
    }

    public function edit(array $params): void
    {
        $user = $this->userService->findUser((int)($params['id'] ?? 0));
        if (!$user) {
            $this->redirect('/admin/users');
            return;
        }

        $branches = $this->userService->listBranches();
        $this->view('Admin.UserFormView', [
            'branches' => $branches,
            'user' => $user,
            'errors' => [],
            'old_data' => [],
        ]);
    }

    public function update(array $params): void
    {
        $userId = (int)($params['id'] ?? 0);
        $user = $this->userService->findUser($userId);
        if (!$user) {
            $this->redirect('/admin/users');
            return;
        }

        $data = $this->request->only(['name', 'email', 'phone', 'role', 'branch_id']);
        $errors = $this->validateInput($data, true);

        if (!empty($errors)) {
            $branches = $this->userService->listBranches();
            $this->view('Admin.UserFormView', [
                'branches' => $branches,
                'user' => $user,
                'errors' => $errors,
                'old_data' => $data,
            ]);
            return;
        }

        if (!$this->userService->updateUser($userId, $data)) {
            $branches = $this->userService->listBranches();
            $errors['general'] = 'Unable to update the user. Please try again.';
            $this->view('Admin.UserFormView', [
                'branches' => $branches,
                'user' => $user,
                'errors' => $errors,
                'old_data' => $data,
            ]);
            return;
        }

        $this->redirect('/admin/users');
    }

    public function delete(array $params): void
    {
        $userId = (int)($params['id'] ?? 0);
        if ($userId > 0) {
            $this->userService->deleteUser($userId);
        }

        $this->redirect('/admin/users');
    }

    private function validateInput(array $data, bool $isUpdate): array
    {
        $errors = [];

        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Full name is required.';
        }

        $email = trim($data['email'] ?? '');
        if (empty($email)) {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email address is required.';
        }

        $phone = trim($data['phone'] ?? '');
        if ($phone !== '' && !preg_match('/^[0-9\-\+\s]+$/', $phone)) {
            $errors['phone'] = 'Please enter a valid phone number.';
        }

        if (!$isUpdate) {
            $password = trim($data['password'] ?? '');
            if (empty($password)) {
                $errors['password'] = 'Password is required for new users.';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters.';
            }
        }

        $role = trim($data['role'] ?? '');
        if (empty($role)) {
            $errors['role'] = 'A role selection is required.';
        }

        return $errors;
    }
}
