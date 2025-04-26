<?php
require_once __DIR__ . '/internal/lib_util.php';
require_once __DIR__ . '/internal/login_info.php';
$focus_jump = true;
require_once __DIR__ . '/partial/page-head.php';
require_once __DIR__ . '/partial/shelf-list.php';
?>

<h1>BookShelf</h1>

<section>
    Menu:
    <ul>
        <li><a href="search/isbn.php">ISBN Search</a></li>
    </ul>
</section>

<section>
    <h2>Storages:</h2>

    <ul>
        <?php foreach (get_bookStoreTree() as $site) { htmlSite($site); } ?>
    </ul>
</section>

<?php require __DIR__ . '/partial/page-end.php'; ?>