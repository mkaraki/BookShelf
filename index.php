<?php require __DIR__ . '/internal/lib_util.php'; ?>
<?php require __DIR__ . '/internal/login_info.php'; ?>
<?php require __DIR__ . '/partial/page-head.php'; ?>

<h1>BookShelf</h1>

<section>
    Menu:
    <ul>
        <li><a href="search/isbn.php">ISBN Search</a></li>
    </ul>
</section>

<section>
    Sites:
    <ul>
        <?php foreach (get_sites() as $site) : ?>
            <li><a href="list/site.php?id=<?= $site['siteId'] ?>"><?= htmlentities($site['siteName']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require __DIR__ . '/partial/page-end.php'; ?>