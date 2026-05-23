<?php

namespace App\Services;

class UserService
{
    public function __construct()
    {
        require_once __DIR__ . '/../../Models/DB.php';
        require_once __DIR__ . '/../../Models/UserModel.php';
        require_once __DIR__ . '/../../Models/BranchModel.php';
    }

    public function searchUsersWithBranch(string $search = '', string $roleFilter = ''): array
    {
        $conn = Connect();
        $users = searchUsersWithBranch($conn, $search, $roleFilter);
        Close($conn);
        return $users;
    }

    public function listBranches(): array
    {
        $conn = Connect();
        $branches = getAdminBranchesList($conn);
        Close($conn);
        return $branches;
    }

    public function findUser(int $id): ?array
    {
        $conn = Connect();
        $user = getUserById($conn, $id);
        Close($conn);
        return $user ?: null;
    }

    public function createUser(array $data): bool
    {
        $conn = Connect();
        $result = createAdminUser(
            $conn,
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['phone'] ?? '',
            $data['role'] ?? '',
            $data['branch_id'] ?? ''
        );
        Close($conn);
        return $result;
    }

    public function updateUser(int $id, array $data): bool
    {
        $conn = Connect();
        $result = updateAdminUser(
            $conn,
            $id,
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? '',
            $data['role'] ?? '',
            $data['branch_id'] ?? ''
        );
        Close($conn);
        return $result;
    }

    public function deleteUser(int $id): bool
    {
        $conn = Connect();
        $result = deleteUserWithDependencies($conn, $id);
        Close($conn);
        return $result;
    }
}
