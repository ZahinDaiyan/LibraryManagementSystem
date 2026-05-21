<?php

function hashPasswordIfNeeded($password)
{
    $passwordInfo = password_get_info($password);

    if ($passwordInfo['algo'] === 0) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    return $password;
}

function getAllUsers($conn)
{
    $sql = "SELECT id, name, email, phone, role, branch_id, is_active FROM users";
    $result = mysqli_query($conn, $sql);
    
    $users = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    return $users;
}

function createUser(
    $conn,
    $name,
    $email,
    $password_hash,
    $phone,
    $role,
    $profile_pic,
    $branch_id
)
{
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);
    $role = mysqli_real_escape_string($conn, $role);
    $profile_pic = mysqli_real_escape_string($conn, $profile_pic);

    $storedPassword = hashPasswordIfNeeded($password_hash);
    $branch_val = $branch_id === '' ? "NULL" : "'" . mysqli_real_escape_string($conn, $branch_id) . "'";
    
    $sql = "INSERT INTO users
            (name, email, password_hash, phone, role, profile_pic, branch_id, is_active, created_at)
            VALUES
            ('$name', '$email', '$storedPassword', '$phone', '$role', '$profile_pic', $branch_val, 1, NOW())";

    return mysqli_query($conn, $sql);
}

function login($conn, $email, $password)
{
    $email = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $user = $result ? mysqli_fetch_assoc($result) : null;

    if ($user) {
        $storedPassword = $user['password_hash'];

        if ($storedPassword === $password || password_verify($password, $storedPassword)) {
            return $user;
        }
    }

    return false;
}

function getUserById($conn, $id)
{
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT * FROM users WHERE id = '$id' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    return $result ? mysqli_fetch_assoc($result) : null;
}

function getUserWithBranchById($conn, $id)
{
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT u.id, u.name, u.email, u.phone, u.role, u.profile_pic, u.branch_id, u.is_active, b.name AS branch_name, b.city AS branch_city, b.address AS branch_address
            FROM users u
            LEFT JOIN branches b ON b.id = u.branch_id
            WHERE u.id = '$id'
            LIMIT 1";

    $result = mysqli_query($conn, $sql);
    return $result ? mysqli_fetch_assoc($result) : null;
}

function updateUser(
    $conn,
    $id,
    $name,
    $email,
    $phone,
    $profile_pic = null
)
{
    $id = mysqli_real_escape_string($conn, $id);
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);

    $pic_sql = "";
    if ($profile_pic !== null) {
        $pic_sql = ", profile_pic = '" . mysqli_real_escape_string($conn, $profile_pic) . "'";
    }

    $sql = "UPDATE users
            SET name = '$name',
                email = '$email',
                phone = '$phone'
                $pic_sql
            WHERE id = '$id'";

    return mysqli_query($conn, $sql);
}

function updateUserPassword($conn, $id, $password)
{
    $id = mysqli_real_escape_string($conn, $id);
    $storedPassword = hashPasswordIfNeeded($password);
    $sql = "UPDATE users SET password_hash = '$storedPassword' WHERE id = '$id'";
    return mysqli_query($conn, $sql);
}

function getUserByEmail($conn, $email)
{
    $email = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    return $result ? mysqli_fetch_assoc($result) : null;
}

function deleteUser($conn, $id)
{
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "DELETE FROM users WHERE id = '$id'";
    return mysqli_query($conn, $sql);
}

function searchUsersWithBranch($conn, $search = '', $role_filter = '')
{
    $where_clauses = [];
    if (!empty($search)) {
        $escaped_search = mysqli_real_escape_string($conn, $search);
        $where_clauses[] = "(u.name LIKE '%$escaped_search%' OR u.email LIKE '%$escaped_search%' OR u.phone LIKE '%$escaped_search%')";
    }
    if (!empty($role_filter)) {
        $escaped_role = mysqli_real_escape_string($conn, $role_filter);
        $where_clauses[] = "u.role = '$escaped_role'";
    }
    
    $where_sql = "";
    if (count($where_clauses) > 0) {
        $where_sql = "WHERE " . implode(' AND ', $where_clauses);
    }
    
    $sql = "SELECT u.*, b.name AS branch_name 
            FROM users u 
            LEFT JOIN branches b ON u.branch_id = b.id 
            $where_sql 
            ORDER BY u.created_at DESC";
            
    $result = mysqli_query($conn, $sql);
    $users = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    return $users;
}

function isEmailTakenByOtherUser($conn, $email, $current_id = null)
{
    $escaped_email = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT id FROM users WHERE email = '$escaped_email'";
    if ($current_id !== null) {
        $escaped_id = mysqli_real_escape_string($conn, $current_id);
        $sql .= " AND id != '$escaped_id'";
    }
    $result = mysqli_query($conn, $sql);
    return ($result && mysqli_num_rows($result) > 0);
}

function createAdminUser($conn, $name, $email, $password, $phone, $role, $branch_id)
{
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);
    $role = mysqli_real_escape_string($conn, $role);
    
    $storedPassword = password_hash($password, PASSWORD_DEFAULT);
    $branch_val = empty($branch_id) ? "NULL" : "'" . mysqli_real_escape_string($conn, $branch_id) . "'";
    
    $sql = "INSERT INTO users (name, email, password_hash, phone, role, branch_id, is_active, created_at) 
            VALUES ('$name', '$email', '$storedPassword', '$phone', '$role', $branch_val, 1, NOW())";
    return mysqli_query($conn, $sql);
}

function updateAdminUser($conn, $id, $name, $email, $phone, $role, $branch_id)
{
    $id = mysqli_real_escape_string($conn, $id);
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);
    $role = mysqli_real_escape_string($conn, $role);
    
    $branch_val = empty($branch_id) ? "NULL" : "'" . mysqli_real_escape_string($conn, $branch_id) . "'";
    
    $sql = "UPDATE users 
            SET name = '$name', email = '$email', phone = '$phone', role = '$role', branch_id = $branch_val 
            WHERE id = '$id'";
    return mysqli_query($conn, $sql);
}

function updateUserRole($conn, $id, $new_role)
{
    $id = mysqli_real_escape_string($conn, $id);
    $new_role = mysqli_real_escape_string($conn, $new_role);
    $sql = "UPDATE users SET role = '$new_role' WHERE id = '$id'";
    return mysqli_query($conn, $sql);
}

function toggleUserStatus($conn, $id)
{
    $id = mysqli_real_escape_string($conn, $id);
    $res = mysqli_query($conn, "SELECT name, is_active FROM users WHERE id = '$id'");
    $user = $res ? mysqli_fetch_assoc($res) : null;
    if (!$user) {
        return false;
    }
    $new_status = $user['is_active'] ? 0 : 1;
    $sql = "UPDATE users SET is_active = '$new_status' WHERE id = '$id'";
    if (mysqli_query($conn, $sql)) {
        $user['new_status'] = $new_status;
        return $user; // Return user array so caller has name/status for audit logging
    }
    return false;
}

function setUserActiveStatus($conn, $id, $is_active)
{
    $id = mysqli_real_escape_string($conn, $id);
    $is_active = $is_active ? 1 : 0;
    $res = mysqli_query($conn, "SELECT name, is_active FROM users WHERE id = '$id'");
    $user = $res ? mysqli_fetch_assoc($res) : null;
    if (!$user) {
        return false;
    }

    $sql = "UPDATE users SET is_active = '$is_active' WHERE id = '$id'";
    if (mysqli_query($conn, $sql)) {
        $user['new_status'] = $is_active;
        return $user;
    }

    return false;
}

?>
