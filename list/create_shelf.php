<?php
require_once __DIR__ . '/../internal/db.php';
require_once __DIR__ . '/../internal/auth.php';
require_once __DIR__ . '/../internal/lib_util.php';

if (
    !isset($_POST['p']) ||
    !is_numeric($_POST['p']) ||
    !isset($_POST['name']) ||
    !is_numeric($_POST['name'])
) {
    die('No required data found');
}

DB::insert('shelfInfo', [
    'shelfNumber' => $_POST['name'],
    'parentCase' => $_POST['p'],
]);

$cId = DB::insertId();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookShelf</title>
</head>

<body>
    Added. <a href="shelf.php?id=<?= $cId ?>">Go to page</a>.
</body>

</html>