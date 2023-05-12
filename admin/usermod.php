<?php
require_once __DIR__ . '/../internal/db.php';
require_once __DIR__ . '/../internal/auth.php';

if ($login_type !== '1') {
    die('You are not admin');
}

if (
    isset($_POST['id']) &&
    is_numeric($_POST['id']) &&
    isset($_POST['action']) &&
    isset($_POST['action'])
) {
    $user = DB::queryFirstRow('SELECT * FROM userInfo WHERE userId = %d', $_POST['id']);
    if ($user === null) {
        die('User not found');
    }

    switch ($_POST['action']) {
        case 'delete':
            DB::delete('userInfo', 'userId=%d', $_POST['id']);
            break;

        default:
            die('Invalid action');
            break;
    }
} else {
    die('No required data found');
}
?>
Processed