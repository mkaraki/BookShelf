<?php require __DIR__ . '/internal/lib_util.php'; ?>
<?php require __DIR__ . '/internal/auth.php'; ?>
<?php require __DIR__ . '/partial/page-head.php'; ?>

<section>
    Sites:
    <ul>
        <?php foreach (get_sites() as $site) : ?>
            <li><a href="site.php?id=<?= $site['siteId'] ?>"><?= htmlentities($site['siteName']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require __DIR__ . '/partial/page-end.php'; ?>