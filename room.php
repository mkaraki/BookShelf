<?php require __DIR__ . '/internal/lib_util.php'; ?>
<?php require __DIR__ . '/internal/login_info.php'; ?>
<?php require __DIR__ . '/partial/page-head.php'; ?>

<?php
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
        <form action="create_case.php">
            <label for="name">Case Name</label>
            <input type="text" name="name" id="name" maxlength="256" required>
            <input type="hidden" name="parent" value="<?= $_GET['id'] ?>">
            <button type="submit">Create Room in site</button>
        </form>
    <?php endif; ?>
</section>

<section>
    Cases:
    <ul>
        <?php foreach (get_cases($_GET['id']) as $v) : ?>
            <li><a href="case.php?id=<?= $v['caseId'] ?>"><?= htmlentities($v['caseName']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require __DIR__ . '/partial/page-end.php'; ?>