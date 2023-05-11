<?php
$login_name = 'Guest';
$login_is = false;

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
    $login_name = 'Guest';
    $login_is = false;
} else {
    $user = DB::queryFirstRow("SELECT * FROM userInfo WHERE userMail=%s", $_SERVER['PHP_AUTH_USER']);
    if ($user == null || $user['userPasswordHash'] == null)
        die('No user found');

    if (!password_verify($_SERVER['PHP_AUTH_PW'], $user['userPasswordHash']))
        die('No user found');

    $login_name = $user['userName'];
}
