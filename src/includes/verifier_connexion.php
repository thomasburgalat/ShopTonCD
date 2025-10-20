<?php

session_start();

$correct_username = 'admin';
$correct_password = 'admin';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $correct_username && $password === $correct_password) {
        $_SESSION['user_logged_in'] = true;
        header('Location: ../../public/index.php');
        exit;
    } else {
        header('Location: ../../public/login.php?error=1');
        exit;
    }
} else {
    header('Location: ../../public/login.php');
    exit;
}
