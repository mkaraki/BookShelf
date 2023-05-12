<?php require __DIR__ . '/../internal/lib_util.php'; ?>
<?php //require __DIR__ . '/../internal/auth.php'; 
?>
<?php

if (!isset($_GET['name']))
    die('No required data found');

$page_title = 'Search publisher - Book Shelf';
?>
<?php
$page_no_menu = true;
require __DIR__ . '/../partial/page-head.php';
?>

<style>
    .err {
        color: red;
    }
</style>

<h1>Publisher: <?= htmlentities($_GET['name']) ?></h1>
<hr />

<section>
    <h2>Search from Database</h2>

    <form action="" method="get">
        <label for="sname">Name</label>
        <input type="text" id="sname" name="name" value="<?= htmlentities($_GET['name']) ?>" required>
        <button type="submit">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Disambiguation</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($_GET['name'])) : ?>
                <?php foreach (search_publishers($_GET['name']) as $v) : ?>
                    <tr>
                        <td><a href="javascript:void(0)" onclick="window.opener.setPublisherId('<?= $v['publisherId'] ?>'); window.close();"><?= htmlentities($v['publisherName']) ?></a></td>
                        <td><?= htmlentities($v['publisherDisambiguation']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<form action="add_publisher.php" method="POST">
    <h2>Add to Database</h2>

    <section>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= htmlentities($_GET['name']) ?>" required>

        <br />

        <label for="name_read">Name (Read)</label>
        <input type="text" id="name_read" name="name_read">

        <br /><br />

        <label for="disambiguation">Disambiguation</label>
        <input type="text" name="disambiguation" id="disambiguation" maxlength="512">
    </section>

    <br />

    <input type="submit" value="Add publisher">
</form>


<?php require __DIR__ . '/../partial/page-end.php'; ?>