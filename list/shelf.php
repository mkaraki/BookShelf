<?php require __DIR__ . '/../internal/lib_util.php'; ?>
<?php require __DIR__ . '/../internal/login_info.php'; ?>
<?php require __DIR__ . '/../partial/page-head.php'; ?>

<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    die('No id specified');

$shelf = get_shelf($_GET['id']);
if (!$shelf)
    die('not found');
$case = get_case($shelf['parentCase']);
?>

<section>
    <h1>
        <small><?= htmlentities($case['caseName']) ?></small><br />
        Shelf <?= $shelf['shelfNumber'] ?>: 01<?= $shelf['shelfId'] ?><?= calc_bcd_cd($shelf['shelfId']) ?>
    </h1>
</section>

<section>
    <?php if ($login_is) : ?>
        <a href="../bookmgr/add_book_form.php?id=<?= $_GET['id'] ?>">Add book</a>
    <?php endif; ?>
</section>

<section>
    <h2>Books</h2>
    <table>
        <thead>
            <tr>
                <th>BookId</th>
                <th>Title</th>
                <th>Publisher</th>
                <th>Authors</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (get_books($_GET['id']) as $v) : ?>
                <?php
                $publisher = null;
                if ($v['publisherId'])
                    $publisher = get_publisher($v['publisherId']);
                ?>
                <tr id="book-<?= $v['uniqueBookId'] ?>">
                    <td>00<?= $v['uniqueBookId'] ?><?= calc_bcd_cd($v['uniqueBookId']) ?></td>
                    <td>
                        <?= htmlentities($v['bookName']) ?>
                        <?php if ($v['bookDisambiguation']) : ?>
                            (<?= htmlentities($v['bookDisambiguation']) ?>)
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($publisher) : ?>
                            <?= htmlentities($publisher['publisherName']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php foreach (get_authors($v['uniqueBookId']) as $author) : ?>
                            <?= htmlentities($author['authorName']) ?>;
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php if ($login_is) : ?>
                            <a href="edit_book.php?id=<?= $v['bookId'] ?>">Edit</a>
                            <a href="delete_book.php?id=<?= $v['bookId'] ?>">Delete</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($login_is) : ?>
                            <form action="delete_book" method="POST">
                                <input type="hidden" name="id" value="<?= $v['bookId'] ?>">
                                <input type="submit" value="Delete">
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require __DIR__ . '/../partial/page-end.php'; ?>