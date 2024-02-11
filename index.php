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
    <h2>Storages:</h2>

    <ul>
        <?php foreach (get_bookStoreTree() as $site) : ?>
            <li>
                <a href="list/site.php?id=<?= $site['siteId'] ?>"><?= htmlentities($site['siteName']) ?></a>
                <ul>
                    <?php foreach ($site['rooms'] as $room) : ?>
                        <li>
                            <a href="list/room.php?id=<?= $room['roomId'] ?>"><?= htmlentities($room['roomName']) ?></a>
                            <ul>
                                <?php foreach ($room['cases'] as $case) : ?>
                                    <li>
                                        <a href="list/case.php?id=<?= $case['caseId'] ?>"><?= htmlentities($case['caseName']) ?></a>
                                        <div>
                                            <?php foreach ($case['shelfs'] as $shelf) : ?>
                                                <span>
                                                    <a href="list/shelf.php?id=<?= $shelf['shelfId'] ?>">Case <?= htmlentities($shelf['shelfNumber']) ?></a>

                                                    (<code>01<?= $shelf['shelfId'] ?><?= calc_bcd_cd($shelf['shelfId']) ?></code>,
                                                    <?= count($shelf['books']) ?> books available)
                                                </span>, 
                                            <?php endforeach; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require __DIR__ . '/partial/page-end.php'; ?>