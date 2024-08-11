<?php
require __DIR__ . '/internal/lib_util.php';
require __DIR__ . '/internal/auth.php';
$focus_jump = true;
require __DIR__ . '/partial/page-head.php';
?>

<h1>BookShelf Dashboard</h1>
<hr />

<section>
    <h2>Menu</h2>
    <ul>
        <li><a href="search/isbn.php">ISBN Search</a></li>
    </ul>
</section>

<section>
    <h2>Create Site</h2>
    <?php if ($login_is) : ?>
        <form action="list/create_site.php" class="mb-3" method="POST">
            <div class=" mb-3">
                <label for="name" class="form-label">Site Name</label>
                <input type="text" name="name" id="name" maxlength="256" required class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Create Site</button>
        </form>
    <?php endif; ?>
</section>

<section>
    <h2>Available Site</h2>
    <ul>
        <?php foreach (get_sites() as $site) : ?>
            <li><a href="list/site.php?id=<?= $site['siteId'] ?>"><?= htmlentities($site['siteName']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require __DIR__ . '/partial/page-end.php'; ?>