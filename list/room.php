<?php
require __DIR__ . '/../internal/lib_util.php';
require __DIR__ . '/../internal/login_info.php';
$focus_jump = true;
require __DIR__ . '/../partial/page-head.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    die('No id specified');

$room = get_room($_GET['id']);
if (!$room)
    die('not found');
?>

<section>
    <h1>Room: <?= htmlentities($room['roomName']) ?></h1>
</section>

<section>
    <?php if ($login_is) : ?>
        <form action="create_case.php" class="mb-3" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Case Name</label>
                <input type="text" name="name" id="name" maxlength="256" required class="form-control">
                <input type="hidden" name="p" value="<?= $_GET['id'] ?>">
            </div>
            <button type="submit" class="btn btn-primary">Create Case</button>
        </form>
    <?php endif; ?>
</section>

<section>
    Cases:
    <ul>
        <?php foreach (get_cases($_GET['id']) as $v) : ?>
            <li>
                <a href="case.php?id=<?= $v['caseId'] ?>"><?= htmlentities($v['caseName']) ?></a>
                (<code>02<?= $v['caseId'] ?><?= calc_bcd_cd($v['caseId']) ?></code>)
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require __DIR__ . '/../partial/page-end.php'; ?>