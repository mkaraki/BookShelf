<?php
require_once __DIR__ . '/../internal/db.php';

if (DB::queryFirstField('SELECT count(*) FROM userInfo WHERE userType = 1 AND userPasswordHash IS NOT NULL') > 0) {
    require_once __DIR__ . '/../internal/auth.php';

    if ($login_type !== '1') {
        die('You are not admin');
    }
};

$user_created = false;

if (
    isset($_POST['name']) &&
    isset($_POST['email']) &&
    isset($_POST['password']) &&
    isset($_POST['password2'])
) {
    if ($_POST['password'] !== $_POST['password2']) {
        die('Passwords do not match');
    }

    $user = DB::queryFirstRow('SELECT * FROM userInfo WHERE userMail = %s', $_POST['email']);
    if ($user !== null) {
        die('User already exists');
    }

    DB::insert('userInfo', [
        'userName' => $_POST['name'],
        'userMail' => $_POST['email'],
        'userPasswordHash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'userType' => 1
    ]);

    $user_created = true;
}

require_once __DIR__ . '/../partial/page-head.php';
?>

<?php if ($user_created) : ?>
    <section>
        User created
    </section>
    <hr />
<?php endif; ?>

<h1>Create admin user</h1>

<form action="" method="POST">
    <section>
        <label for="name">Name</label>
        <input type="text" name="name" id="name">
    </section>

    <section>
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email">
    </section>

    <section>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
        <br />
        <label for="password2">Confirm Password</label>
        <input type="password" name="password2" id="password2">
    </section>

    <input type="submit" value="Create admin user">
</form>

<?php require_once __DIR__ . '/../partial/page-end.php'; ?>