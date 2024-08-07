<?php
require_once __DIR__ . '/../internal/login_info.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlentities($page_title ?? 'BookShelf') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style>
        header {
            border-bottom: 1px solid black;
        }
        .table-action-field>form {
            display: inline-block;
        }
    </style>
</head>

<body class="m-3">
    <?php if (!isset($page_no_menu)) : ?>
        <header>
            Hello <?= $login_name ?>.
            <?php if (!$login_is) : ?>
                <a href="dash.php">Login</a>
            <?php endif; ?>
        </header>
    <?php endif; ?>