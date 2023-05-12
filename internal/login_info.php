<?php
require_once __DIR__ . '/db.php';

$login_id = -1;
$login_name = 'Guest';
$login_is = false;
$login_type = -1;

if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    $user = DB::queryFirstRow("SELECT * FROM userInfo WHERE userMail=%s", $_SERVER['PHP_AUTH_USER']);
    if (
        $user !== null &&
        $user['userPasswordHash'] !== null &&
        password_verify($_SERVER['PHP_AUTH_PW'], $user['userPasswordHash'])
    ) {
        $login_id = $user['userId'];
        $login_name = $user['userName'];
        $login_is = true;
        $login_type = $user['userType'];
    }
}
