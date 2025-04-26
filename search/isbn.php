<?php require __DIR__ . '/../internal/lib_util.php'; ?>
<?php

$page_title = 'Search ISBN - Book Shelf';
$focus_jump = true;
require __DIR__ . '/../partial/page-head.php';
?>

<h1>ISBN Search</h1>
<hr />

<section>
    <form action="" method="get" class="mb-3">
        <div class="mb-3">
            <label for="sname" class="form-label">ISBN</label>
            <input type="text" id="sname" name="isbn" value="" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="code.php" class="btn btn-secondary" role="button">Barcode</a>
    </form>

    <?php if (isset($_GET['isbn']) && is_numeric($_GET['isbn']) && strlen($_GET['isbn']) === 13) : ?>
        <p>Query: isbn=<?= htmlentities($_GET['isbn']) ?></p>
        <table class="table">
            <thead>
                <tr>
                    <th>Title (Publisher)</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (search_book_isbn($_GET['isbn']) as $v) : ?>
                    <tr>
                        <td>
                            <a><?= htmlentities($v['bookName']) ?></a>
                            <?php if ($v['publisherName'] != null) : ?>
                                (<?= htmlentities($v['publisherName']) ?>)
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="javascript:void(0)" onclick="alert(<?= htmlspecialchars(json_encode('Shelf ' . $v['shelfNumber'] . ', ' . $v['caseName'] . ', ' . $v['roomName'] . ', ' . $v['siteName'])) ?>)">
                                <?= htmlentities($v['siteName']) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<script>
    document.onload = () => {
        document.getElementById('sname').focus();
    };
</script>

<?php require __DIR__ . '/../partial/page-end.php'; ?>