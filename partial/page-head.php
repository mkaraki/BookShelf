<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlentities($page_title ?? 'BookShelf') ?></title>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            margin: 5px;
            width: calc(100vw - 10px);
        }

        label {
            margin-top: 30px;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            display: block;
            width: calc(100% - 10px);
            margin: 5px;
        }

        input[type="submit"] {
            margin: 15px;
            margin-left: 0;
        }

        header {
            border-bottom: 1px solid black;
        }
    </style>
</head>

<body>
    <?php if (!isset($page_no_menu)) : ?>
        <header>
            Hello <?= $login_name ?>.
            <?php if (!$login_is) : ?>
                <a href="dash.php">Login</a>
            <?php endif; ?>
        </header>
    <?php endif; ?>