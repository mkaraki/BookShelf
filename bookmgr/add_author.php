<?php
require_once __DIR__ . '/../internal/db.php';
//require_once __DIR__ . '/../internal/auth.php';
require_once __DIR__ . '/../internal/lib_util.php';

if (
    !isset($_POST['name']) ||
    empty($_POST['name'])
) {
    die('No required data found');
}

$nameRead = null;
if (isset($_POST['name_read']) && !empty($_POST['name_read']))
    $nameRead = $_POST['name_read'];

$disambiguation = null;
if (isset($_POST['disambiguation']) && !empty($_POST['disambiguation']))
    $disambiguation = $_POST['disambiguation'];

DB::insert('authorInfo', [
    'authorName' => $_POST['name'],
    'authorRead' => $nameRead,
    'authorDisambiguation' => $disambiguation,
]);

$authorId = DB::insertId();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back to previous screen</title>
</head>

<body>
    <button onclick="insrt()">Back to previous screen</button>

    <script>
        function insrt() {
            const res = window.opener.setAuthorId(<?= $_POST['num'] ?? '0' ?>, '<?= $authorId ?>');
            if (res)
                window.close();
        }

        window.onload = insrt;
    </script>
</body>

</html>