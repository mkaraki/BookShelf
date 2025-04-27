<?php require __DIR__ . '/../internal/lib_util.php'; ?>
<?php

$page_title = 'Search Book title - Book Shelf';
$focus_jump = true;
require __DIR__ . '/../partial/page-head.php';
?>

<h1>Title Search</h1>
<hr />

<section>
    <form action="" method="get" class="mb-3">
        <div class="mb-3">
            <label for="stitle" class="form-label">Title (LIKE search)</label>
            <input type="text" id="stitle" name="title" value="" required autofocus class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php if (!empty($_GET['title'])) : ?>
        <p>Query: title_like=<?= htmlentities($_GET['title']) ?></p>
        <table class="table">
            <thead>
            <tr>
                <th>Title (Publisher)</th>
                <th>ISBN</th>
                <th>Location</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach (search_book_title(trim($_GET['title'])) as $v) : ?>
                <tr>
                    <td>
                        <a><?= htmlentities($v['bookName']) ?></a>
                        <?php if ($v['publisherName'] != null) : ?>
                            (<?= htmlentities($v['publisherName']) ?>)
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= htmlentities($v['isbn']) ?>
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
    setInterval(()=>{
        document.getElementById('stitle').focus();
    }, 0);
</script>

<?php require __DIR__ . '/../partial/page-end.php'; ?>
