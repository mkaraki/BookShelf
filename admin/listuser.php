<?php
require_once __DIR__ . '/../internal/db.php';
require_once __DIR__ . '/../internal/auth.php';

if ($login_type !== '1') {
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
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" required class="form-control" />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" required class="form-control" />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" />
            </div>

            <div class="mb-3">
                <input type="checkbox" name="loginable" id="loginable" checked value="1" class="form-check-input" />
                <label for="loginable" class="form-check-label">Able to login?</label>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select name="type" id="type" class="form-select">
                    <option value="0">User</option>
                    <option value="1">Admin</option>
                    <option value="2">User Group (Virtual User)</option>
                </select>
            </div>
        </section>

        <div class="mb-3">
            <input type="submit" value="Create" class="btn btn-primary">
        </div>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col">E-mail</th>
            <th scope="col">Type</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (DB::query('SELECT * FROM userInfo') as $v) : ?>
            <tr>
                <td><?= htmlentities($v['userName']) ?></td>
                <td><?= htmlentities($v['userMail']) ?></td>
                <td>
                    <?= htmlentities($v['userType']) ?>
                    <?php if ($v['userPasswordHash'] === null) : ?>
                        <span class="badge bg-danger">No Login</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($login_id !== $v['userId']) : ?>
                        <form action="usermod.php" method="post" onsubmit="return confirm('Really?')">
                            <input type="hidden" name="id" value="<?= $v['userId'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="submit" value="Delete" class="btn btn-danger btn-sm">
                        </form>
                    <?php else : ?>
                        <span class="badge bg-secondary">You</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../partial/page-end.php'; ?>