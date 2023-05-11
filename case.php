<?php require __DIR__ . '/internal/lib_util.php'; ?>
<?php require __DIR__ . '/internal/login_info.php'; ?>
<?php require __DIR__ . '/partial/page-head.php'; ?>

<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    die('No id specified');

$case = get_case($_GET['id']);
if (!$case)
    die('not found');
?>

<section>
    <h1>Case: <?= htmlentities($case['caseName']) ?></h1>
</section>

<section>
    <?php if ($login_is) : ?>
        <form action="create_shelf.php">
            <label for="name">Shelf Number</label>
            <input type="number" name="number" id="name" required min="0" max="255">
            <input type="hidden" name="parent" value="<?= $_GET['id'] ?>">
            <button type="submit">Create Shelf in case</button>
        </form>
    <?php endif; ?>
</section>

<section>
    Shelf:
    <ul>
        <?php foreach (get_shelfs($_GET['id']) as $v) : ?>
            <li><a href="shelf.php?id=<?= $v['shelfId'] ?>"><?= $v['shelfNumber'] ?>: 01<?= $v['shelfId'] ?><?= calc_bcd_cd($v['shelfId']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require __DIR__ . '/partial/page-end.php'; ?>