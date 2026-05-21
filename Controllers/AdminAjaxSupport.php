<?php

if (!function_exists('adminWantsJson')) {
    function adminWantsJson()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
            || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}

if (!function_exists('adminJsonResponse')) {
    function adminJsonResponse($success, $message, $extra = array(), $statusCode = 200)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode(array_merge(array(
            'success' => (bool)$success,
            'message' => $message
        ), $extra));
        exit();
    }
}

if (!function_exists('adminFinishResponse')) {
    function adminFinishResponse($expectsJson, $success, $message, $redirect, $extra = array(), $statusCode = 200)
    {
        if ($expectsJson) {
            adminJsonResponse($success, $message, array_merge(array('redirect' => $redirect), $extra), $statusCode);
        }

        if ($success) {
            $_SESSION['msg'] = $message;
        } else {
            $_SESSION['error'] = $message;
        }

        if ($redirect) {
            header('Location: ' . $redirect);
            exit();
        }
    }
}
