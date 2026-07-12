<?php
require_once __DIR__ . '/../internal/db.php';
require_once __DIR__ . '/../internal/auth.php';
require_once __DIR__ . '/../internal/lib_util.php';

if ($login_type !== '1') {
    die('You are not admin');
}

$sites = DB::query('SELECT * FROM siteInfo');
$rooms = DB::query('SELECT * FROM roomInfo');
$cases = DB::query('SELECT * FROM caseInfo');
$shelves = DB::query('SELECT * FROM shelfInfo');

$allShelveIds = array_column($shelves, 'shelfId');

$authors = DB::query('SELECT * FROM authorInfo');
$publishers = DB::query('SELECT * FROM publisherInfo');

$bookCollection = DB::query('SELECT * FROM bookCollection');
$originalBookCollectionSize = count($bookCollection);

foreach ($bookCollection as $key => $book) {
    if (!in_array($book['belongShelf'], $allShelveIds)) {
        $bookCollection[$key]['belongShelf'] = null;
    }

    $authorLinks = DB::query('SELECT * FROM bookAuthorLinker WHERE uniqueBookId=%i', $book['uniqueBookId']);
    $bookCollection[$key]['authorIds'] = [];
    foreach ($authorLinks as $linkKey => $link) {
        $bookCollection[$key]['authorIds'][] = $link['authorId'];
    }
}

// Remove null from bookCollection
$bookCollection = array_filter($bookCollection, function ($book) {
    return $book['belongShelf'] !== null;
});
$filteredBookCollectionSize = count($bookCollection);

$nonUniqueIsbnBookIsbns = DB::query('SELECT isbn FROM bookCollection WHERE isbn IS NOT NULL GROUP BY isbn HAVING COUNT(isbn) > 1');

$nonUniqueIsbnBooks = [];
foreach ($nonUniqueIsbnBookIsbns as $isbn) {
    $books = DB::query('SELECT * FROM bookCollection WHERE isbn=%s', $isbn['isbn']);
    $nonUniqueIsbnBooks = array_merge($nonUniqueIsbnBooks, $books);
}

$sameNameBookNames = DB::query('SELECT bookName FROM bookCollection WHERE bookName IS NOT NULL GROUP BY bookName HAVING COUNT(bookName) > 1');

$sameNameBooks = [];
foreach ($sameNameBookNames as $bookName) {
    $books = DB::query('SELECT * FROM bookCollection WHERE bookName=%s', $bookName['bookName']);
    $sameNameBooks = array_merge($sameNameBooks, $books);
}

$json_str = json_encode([
    'sites' => $sites,
    'rooms' => $rooms,
    'cases' => $cases,
    'shelves' => $shelves,
    'authors' => $authors,
    'publishers' => $publishers,
    'bookCollection' => $bookCollection,
], JSON_PRETTY_PRINT);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>BookShelf Exporter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>BookShelf Exporter</h1>
    
    <section>
        <h2>Non Unique ISBN Books</h2>
        <p>
            Duplicated ISBNs: <?= count($nonUniqueIsbnBookIsbns) ?>,
            Duplicated ISBN books: <?= count($nonUniqueIsbnBooks) ?>
        </p>
        <table>
            <tr>
                <th>Code</th>
                <th>Id</th>
                <th>Title</th>
                <th>Publisher</th>
                <th>Authors</th>
                <th>Shelf Id</th>
            </tr>
            <?php foreach ($nonUniqueIsbnBooks as $v) : ?>
                <?php
                $publisher = null;
                if ($v['publisherId'])
                    $publisher = get_publisher($v['publisherId']);
                ?>
                <tr id="book-<?= $v['uniqueBookId'] ?>">
                    <td>00<?= $v['uniqueBookId'] ?><?= calc_bcd_cd($v['uniqueBookId']) ?></td>
                    <td><?= $v['uniqueBookId'] ?></td>
                    <td>
                        <?= htmlentities($v['bookName']) ?>
                        <?php if ($v['bookDisambiguation']) : ?>
                            (<?= htmlentities($v['bookDisambiguation']) ?>)
                        <?php endif; ?>
                        <?php if ($v['isbn']) : ?>
                            <br />
                            <small><?= htmlentities($v['isbn']) ?></small>
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
                        <?= htmlentities($v['belongShelf']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
    
    <section>
        <h2>Same Name Books</h2>
        <p>
            Same name books: <?= count($sameNameBooks) ?>,
            Same name book names: <?= count($sameNameBookNames) ?>
        </p>
        <table>
            <tr>
                <th>Code</th>
                <th>Id</th>
                <th>Title</th>
                <th>Publisher</th>
                <th>Authors</th>
                <th>Shelf Id</th>
            </tr>
            <?php foreach ($sameNameBooks as $v) : ?>
                <?php
                $publisher = null;
                if ($v['publisherId'])
                    $publisher = get_publisher($v['publisherId']);
                ?>
                <tr id="book-<?= $v['uniqueBookId'] ?>">
                    <td>00<?= $v['uniqueBookId'] ?><?= calc_bcd_cd($v['uniqueBookId']) ?></td>
                    <td><?= $v['uniqueBookId'] ?></td>
                    <td>
                        <?= htmlentities($v['bookName']) ?>
                        <?php if ($v['bookDisambiguation']) : ?>
                            (<?= htmlentities($v['bookDisambiguation']) ?>)
                        <?php endif; ?>
                        <?php if ($v['isbn']) : ?>
                            <br />
                            <small><?= htmlentities($v['isbn']) ?></small>
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
                        <?= htmlentities($v['belongShelf']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
    
    <section>
        <h2>Export</h2>
        <p>
            Original book collection size: <?= $originalBookCollectionSize ?>, 
            Filtered book collection size (without null (invalid Shelf) belongShelf): <?= $filteredBookCollectionSize ?>
        </p>
        <textarea style="width: 100%; height: 400px;"><?= htmlentities($json_str) ?></textarea>
    </section>
</body>
</html>