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
    $storedPassword = hashPasswordIfNeeded($password_hash);
    $branch_val = $branch_id == '' ? "NULL" : "'$branch_id'";
    
    $sql = "INSERT INTO users
            (name, email, password_hash, phone, role, profile_pic, branch_id, is_active, created_at)
            VALUES
            ('$name', '$email', '$storedPassword', '$phone', '$role', '$profile_pic', $branch_val, 1, NOW())";

    return mysqli_query($conn, $sql);
}

function login($conn, $email, $password)
{
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

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
    $sql = "SELECT * FROM users WHERE id = '$id' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

function getUserWithBranchById($conn, $id)
{
    $sql = "SELECT u.id, u.name, u.email, u.phone, u.role, u.profile_pic, u.branch_id, u.is_active, b.name AS branch_name, b.city AS branch_city, b.address AS branch_address
            FROM users u
            LEFT JOIN branches b ON b.id = u.branch_id
            WHERE u.id = '$id'
            LIMIT 1";

    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
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
    $pic_sql = "";
    if ($profile_pic !== null) {
        $pic_sql = ", profile_pic = '$profile_pic'";
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
    $storedPassword = hashPasswordIfNeeded($password);
    $sql = "UPDATE users SET password_hash = '$storedPassword' WHERE id = '$id'";
    return mysqli_query($conn, $sql);
}


function getUserByEmail($conn, $email)
{
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

function deleteUser($conn, $id)
{
    $sql = "DELETE FROM users WHERE id = '$id'";
    return mysqli_query($conn, $sql);
}

?>
