<?php require __DIR__ . '/internal/lib_util.php'; ?>
<?php require __DIR__ . '/internal/login_info.php'; ?>
<?php require __DIR__ . '/partial/page-head.php'; ?>

<section>
    Hello <?= $login_name ?>.
    <?php if (!$login_is) : ?>
        <a href="dash.php">Login</a>
    <?php endif; ?>
</section>

<section>
    Sites:
    <ul>
        <?php foreach (get_sites() as $site) : ?>
            <li><a href="site.php?id=<?= $site['siteId'] ?>"><?= htmlentities($site['siteName']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require __DIR__ . '/partial/page-end.php'; ?>