<?php
// route: lib/routes/auth/authentication.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include Auth class (adjust path if your structure differs)
include_once('../../function/authFunction.php');


$authObj = new Auth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // get inputs safely
    $email = isset($_POST['userName']) ? trim($_POST['userName']) : '';
    $password = isset($_POST['userPwd']) ? $_POST['userPwd'] : '';

    // call authentication and echo JSON response
    echo $authObj->authentication($email, $password);
    exit;
} else {
    echo json_encode([
        'ok' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}
