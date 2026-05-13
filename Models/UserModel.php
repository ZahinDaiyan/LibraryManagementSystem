<?php

function getAllUsers($conn)
{
    $sql = "SELECT * FROM users";

    $result = mysqli_query($conn, $sql);

    $users = array();

    if (mysqli_num_rows($result) > 0) {

        while ($row = mysqli_fetch_assoc($result)) {

            $user = array(
                "id" => $row['id'],
                "name" => $row['name'],
                "email" => $row['email'],
                "phone" => $row['phone'],
                "role" => $row['role'],
                "branch_id" => $row['branch_id'],
                "is_active" => $row['is_active']
            );

            array_push($users, $user);
        }
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
    $sql = "INSERT INTO users
    (name, email, password_hash, phone, role, profile_pic, branch_id, is_active, created_at)

    VALUES

    ('$name', '$email', '$password_hash', '$phone',
    '$role', '$profile_pic', '$branch_id', 1, NOW())";

    return mysqli_query($conn, $sql);
}

function login($conn, $email, $password_hash)
{
    $sql = "SELECT * FROM users
            WHERE email='$email'
            AND password_hash='$password_hash'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        return mysqli_fetch_assoc($result);

    }

    return false;
}


function getUserById($conn, $id)
{
    $sql = "SELECT * FROM users WHERE id='$id'";

    $result = mysqli_query($conn, $sql);

    return mysqli_fetch_assoc($result);
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
            SET
            name='$name',
            email='$email',
            phone='$phone'
            WHERE id='$id'";

    return mysqli_query($conn, $sql);
}

function deleteUser($conn, $id)
{
    $sql = "DELETE FROM users WHERE id='$id'";

    return mysqli_query($conn, $sql);
}

?>
