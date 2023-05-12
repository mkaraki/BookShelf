<?php
require_once __DIR__ . '/../internal/db.php';
require_once __DIR__ . '/../internal/auth.php';

if ($login_type !== 1) {
    die('You are not admin');
}

require_once __DIR__ . '/../partial/page-head.php';
?>

<h1>User Management</h1>

<hr />

<div>
    <h2>Create User</h2>
    <form action="createuser.php" method="POST">
        <section>
            <label for="name">Name</label>
            <input type="text" name="name" id="name" required />

            <br />

            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" required />

            <br />

            <label for="password">Password</label>
            <input type="password" name="password" id="password" />

            <br />

            <label for="loginable">Able to login?</label>
            <input type="checkbox" name="loginable" id="loginable" checked value="1" />

            <br />

            <label for="type">Type</label>
            <select name="type" id="type">
                <option value="0">User</option>
                <option value="1">Admin</option>
                <option value="2">User Group (Virtual User)</option>
            </select>
        </section>
        <input type="submit" value="Create">
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>E-mail</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (DB::query('SELECT * FROM userInfo') as $v) : ?>
            <tr>
                <td><?= htmlentities($v['userName']) ?></td>
                <td><?= htmlentities($v['userMail']) ?></td>
                <td><?= htmlentities($v['userType']) ?></td>
                <td></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../partial/page-end.php'; ?>