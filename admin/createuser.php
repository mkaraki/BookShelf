<?php
require_once __DIR__ . '/../internal/db.php';
require_once __DIR__ . '/../internal/auth.php';

if ($login_type !== '1') {
    die('You are not admin');
}

if (
    isset($_POST['name']) &&
    !empty($_POST['name']) &&
    isset($_POST['email']) &&
    filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) &&
    isset($_POST['password']) &&
    isset($_POST['type']) &&
    is_numeric($_POST['type'])
) {
    $user = DB::queryFirstRow('SELECT * FROM userInfo WHERE userEmail = %s', $_POST['email']);
    if ($user !== null) {
        die('User already exists');
    }

    $type = intval($_POST['type']);
    if ($type < 0 || $type > 2)
        die('Invalid type');

    $pwd = $_POST['password'] ?? null;
    if (!isset($_POST['loginable']) && $_POST['loginable'] === '1' && $pwd !== null)
        $pwd = password_hash($pwd, PASSWORD_DEFAULT);

    DB::insert('userInfo', [
        'userName' => $_POST['name'],
        'userMail' => $_POST['email'],
        'userPasswordHash' => $pwd,
        'userType' => $type,
    ]);
}
