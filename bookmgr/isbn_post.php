<?php
require_once __DIR__ . '/../internal/lib_util.php';


$ret = [];

if (isset($_POST['isbn']) && is_numeric($_POST['isbn'])) {
    $ret['isbn'] = search_book_isbn($_POST['isbn']);
}

if (isset($_POST['publisher']) && !empty($_POST['publisher'])) {
    $publisher = search_publishers($_POST['publisher']);
    $ret['publisher'] = $publisher;
}

if (isset($_POST{
    'authornum'}) && is_numeric($_POST['authornum'])) {
    $ret['authors'] = [];
    for ($i = 1; $i <= $_POST['authornum']; $i++) {
        if (!isset($_POST['author' . $i]) || empty($_POST['author' . $i]))
            continue;
        $ret['authors'][$i - 1] = search_authors($_POST['author' . $i]);
    }
}

header('Content-Type: application/json');
print(json_encode($ret));
