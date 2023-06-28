<?php
require_once __DIR__ . '/../internal/db.php';
require_once __DIR__ . '/../internal/auth.php';
require_once __DIR__ . '/../internal/lib_util.php';

if (
    !isset($_POST['name']) ||
    empty($_POST['name']) ||
    !isset($_POST['shelfId']) ||
    !is_numeric($_POST['shelfId'])
) {
    die('No required data found');
}

$linkAuthorIds = [];

$authorNum = intval($_POST['authorNum'] ?? '0');
for ($i = 1; $i <= $authorNum; $i++) {
    if (!isset($_POST['internalAuthorId' . $i]) || !is_numeric($_POST['internalAuthorId' . $i]))
        continue;
    $linkAuthorIds[] = $_POST['internalAuthorId' . $i];
}

$publisherId = null;
if (isset($_POST['internalPublisherId']) && is_numeric($_POST['internalPublisherId']))
    $publisherId = $_POST['internalPublisherId'];

$bookRead = null;
if (isset($_POST['name_read']) && !empty($_POST['name_read']))
    $bookRead = $_POST['name_read'];

$isbn = null;
if (isset($_POST['isbn']) && is_numeric($_POST['isbn']))
    $isbn = $_POST['isbn'];

$disambiguation = null;
if (isset($_POST['disambiguation']) && !empty($_POST['disambiguation']))
    $disambiguation = $_POST['disambiguation'];

DB::insert('bookCollection', [
    'belongShelf' => $_POST['shelfId'],
    'bookName' => $_POST['name'],
    'bookRead' => $bookRead,
    'publisherId' => $publisherId,
    'isbn' => $isbn,
    'bookDisambiguation' => $disambiguation,
]);

$bookId = DB::insertId();

foreach ($linkAuthorIds as $v) {
    DB::insert('bookAuthorLinker', [
        'uniqueBookId' => $bookId,
        'authorId' => $v,
    ]);
}

http_response_code(303);
header('Location: add_book_form.php?added=' . $_POST['shelfId'] . '&id=' . $bookId);
