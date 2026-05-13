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
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $users = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'role' => $row['role'],
            'branch_id' => $row['branch_id'],
            'is_active' => $row['is_active']
        );
    }

    mysqli_stmt_close($stmt);

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
    $storedPassword = hashPasswordIfNeeded($password_hash);
    $sql = "INSERT INTO users
            (name, email, password_hash, phone, role, profile_pic, branch_id, is_active, created_at)
            VALUES
            (?, ?, ?, ?, ?, ?, NULLIF(?, ''), 1, NOW())";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        'sssssss',
        $name,
        $email,
        $storedPassword,
        $phone,
        $role,
        $profile_pic,
        $branch_id
    );

    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function login($conn, $email, $password)
{
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

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
    $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $user;
}

function getUserWithBranchById($conn, $id)
{
    $sql = "SELECT u.id, u.name, u.email, u.phone, u.role, u.profile_pic, u.branch_id, u.is_active, b.name AS branch_name, b.city AS branch_city, b.address AS branch_address
            FROM users u
            LEFT JOIN branches b ON b.id = u.branch_id
            WHERE u.id = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $user;
}

function updateUser(
    $conn,
    $id,
    $name,
    $email,
    $phone
)
{
    $sql = "UPDATE users
            SET name = ?,
                email = ?,
                phone = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $phone, $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function updateUserPassword($conn, $id, $password)
{
    $storedPassword = hashPasswordIfNeeded($password);
    $sql = "UPDATE users SET password_hash = ? WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $storedPassword, $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}


function getUserByEmail($conn, $email)
{
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $user;
}

function deleteUser($conn, $id)
{
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

?>
