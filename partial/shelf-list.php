<?php
require __DIR__ . '/../internal/lib_util.php';

function htmlShelf($shelf) { ?>
    <span>
        <a href="/list/shelf.php?id=<?= $shelf['shelfId'] ?>">Shelf <?= htmlentities($shelf['shelfNumber']) ?></a>

        (<code>01<?= $shelf['shelfId'] ?><?= calc_bcd_cd($shelf['shelfId']) ?></code>,
        <?= count($shelf['books']) ?> books available)
    </span>,
<?php }
function htmlCase($case) { ?>
    <li>
        <a href="/list/case.php?id=<?= $case['caseId'] ?>"><?= htmlentities($case['caseName']) ?></a>
        <div>
            <?php foreach ($case['shelfs'] as $shelf) { htmlShelf($shelf); } ?>
        </div>
    </li>
<?php }
function htmlRoom($room) {?>
    <li>
        <a href="/list/room.php?id=<?= $room['roomId'] ?>"><?= htmlentities($room['roomName']) ?></a>
        <ul>
            <?php foreach ($room['cases'] as $case) { htmlCase($case); } ?>
        </ul>
    </li>
<?php }
function htmlSite($site) {?>
    <li>
        <a href="/list/site.php?id=<?= $site['siteId'] ?>"><?= htmlentities($site['siteName']) ?></a>
        <ul>
            <?php foreach ($site['rooms'] as $room) { htmlRoom($room); } ?>
        </ul>
    </li>
<?php } ?>
