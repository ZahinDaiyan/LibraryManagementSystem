<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\Url;
use App\Helpers\Csrf;
use App\Middleware\AdminMiddleware;
use App\Services\UserService;

class UserController extends Controller
{
    private UserService $userService;
    protected AdminMiddleware $middleware;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->userService = new UserService();
        $this->middleware = new AdminMiddleware();
    }

    private function authorize(): bool
    {
        return $this->middleware->handle($this->request, $this->response);
    }

    public function index(): void
    {
        if (!$this->authorize()) {
            return;
        }

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
        if (!$this->authorize()) {
            return;
        }

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
        if (!$this->authorize()) {
            return;
        }

        $data = $this->request->only(['name', 'email', 'password', 'phone', 'role', 'branch_id']);

        if (!Csrf::verify($this->request->input('_csrf'))) {
            $branches = $this->userService->listBranches();
            $errors = ['general' => 'CSRF token verification failed.'];
            $this->view('Admin.UserFormView', [
                'branches' => $branches,
                'errors' => $errors,
                'old_data' => $data,
                'user' => null,
            ]);
            return;
        }

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

        $this->with('msg', 'User account created successfully.');
        $this->redirect('/admin/users');
    }

    public function edit(array $params): void
    {
        if (!$this->authorize()) {
            return;
        }

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
        if (!$this->authorize()) {
            return;
        }

        $userId = (int)($params['id'] ?? 0);
        $user = $this->userService->findUser($userId);
        if (!$user) {
            $this->redirect('/admin/users');
            return;
        }

        $data = $this->request->only(['name', 'email', 'phone', 'role', 'branch_id']);

        if (!Csrf::verify($this->request->input('_csrf'))) {
            $branches = $this->userService->listBranches();
            $errors = ['general' => 'CSRF token verification failed.'];
            $this->view('Admin.UserFormView', [
                'branches' => $branches,
                'user' => $user,
                'errors' => $errors,
                'old_data' => $data,
            ]);
            return;
        }

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

        $this->with('msg', 'User details updated successfully.');
        $this->redirect('/admin/users');
    }

    public function delete(array $params): void
    {
        if (!$this->authorize()) {
            return;
        }

        if (!$this->request->isPost()) {
            $this->with('error', 'Invalid request method.');
            $this->redirect('/admin/users');
            return;
        }

        if (!Csrf::verify($this->request->input('_csrf'))) {
            $this->with('error', 'CSRF token verification failed.');
            $this->redirect('/admin/users');
            return;
        }

        $userId = (int)($params['id'] ?? 0);
        if ($userId > 0) {
            if ($this->userService->deleteUser($userId)) {
                $this->with('msg', 'User profile deleted successfully.');
            } else {
                $this->with('error', 'Unable to delete user profile.');
            }
        }

        $this->redirect('/admin/users');
    }

    public function toggleStatus(array $params): void
    {
        if (!$this->authorize()) {
            return;
        }

        if (!$this->request->isPost()) {
            $this->with('error', 'Invalid request method.');
            $this->redirect('/admin/users');
            return;
        }

        if (!Csrf::verify($this->request->input('_csrf'))) {
            $this->with('error', 'CSRF token verification failed.');
            $this->redirect('/admin/users');
            return;
        }

        $userId = (int)($params['id'] ?? 0);
        if ($userId > 0) {
            if ($this->userService->toggleStatus($userId)) {
                $this->with('msg', 'User status toggled successfully.');
            } else {
                $this->with('error', 'Unable to toggle user status.');
            }
        }

        $this->redirect('/admin/users');
    }

    public function changeRole(array $params): void
    {
        if (!$this->authorize()) {
            return;
        }

        if (!$this->request->isPost()) {
            $this->with('error', 'Invalid request method.');
            $this->redirect('/admin/users');
            return;
        }

        if (!Csrf::verify($this->request->input('_csrf'))) {
            $this->with('error', 'CSRF token verification failed.');
            $this->redirect('/admin/users');
            return;
        }

        $userId = (int)($params['id'] ?? 0);
        $role = trim($this->request->input('role', ''));

        if (empty($role)) {
            $this->with('error', 'Role selection is required.');
            $this->redirect('/admin/users');
            return;
        }

        if ($userId > 0) {
            if ($this->userService->changeRole($userId, $role)) {
                $this->with('msg', 'User role updated successfully.');
            } else {
                $this->with('error', 'Unable to change user role.');
            }
        }

        $this->redirect('/admin/users');
    }

    public function resetPassword(array $params): void
    {
        if (!$this->authorize()) {
            return;
        }

        if (!$this->request->isPost()) {
            $this->with('error', 'Invalid request method.');
            $this->redirect('/admin/users');
            return;
        }

        if (!Csrf::verify($this->request->input('_csrf'))) {
            $this->with('error', 'CSRF token verification failed.');
            $this->redirect('/admin/users');
            return;
        }

        $userId = (int)($params['id'] ?? 0);
        $password = trim($this->request->input('password', ''));

        if (empty($password)) {
            $this->with('error', 'Password is required.');
            $this->redirect('/admin/users');
            return;
        }

        if (strlen($password) < 6) {
            $this->with('error', 'Password must be at least 6 characters.');
            $this->redirect('/admin/users');
            return;
        }

        if ($userId > 0) {
            if ($this->userService->resetPassword($userId, $password)) {
                $this->with('msg', 'Password reset successfully.');
            } else {
                $this->with('error', 'Unable to reset password.');
            }
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
