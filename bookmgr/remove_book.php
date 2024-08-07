<?php
require_once __DIR__ . '/../internal/db.php';
require_once __DIR__ . '/../internal/auth.php';
require_once __DIR__ . '/../internal/lib_util.php';

if (
    !isset($_POST['id']) ||
    empty($_POST['id']) ||
    !is_numeric($_POST['id'])
) {
    http_response_code(400);
    die('No required data found');
}

$bookId = $_POST['id'];
$shelfId = DB::queryFirstField("SELECT belongShelf FROM bookCollection WHERE uniqueBookId = %i", $bookId);

if ($shelfId === null) {
    http_response_code(404);
    die("No such book");
}

DB::delete('bookCollection', ['uniqueBookId' => $bookId]);

http_response_code(303);
header('Location: ../list/shelf.php?id=' . $shelfId);
