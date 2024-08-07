<?php
require __DIR__ . '/../internal/lib_util.php';
$code = $_GET['code'];

if (empty($code) || !is_numeric($code)) {
    http_response_code(400);
    die("Invalid Code");
}

if (strlen($code) < 4) {
    http_response_code(400);
    die("Invalid Code");
}

if (str_starts_with($code, '978') && strlen($code) == 13) {
    http_response_code(301);
    header('Location: isbn.php?isbn=' . $code);
    exit;
}

$code_type = substr($code, 0, 2);
$code_content = substr($code, 2, strlen($code) - 3);
$bcd = calc_bcd_cd($code_content);
$bcd_expect = substr($bcd, strlen($bcd) - 2, 1);

if ($bcd != $bcd_expect) {
    http_response_code(400);
    die("Invalid Code. Code check fail.");
}

switch ($code_type) {
    case '00':
        // Book. ToDo.
        http_response_code(400);
        die('Book code is not supported yet.');
        break;

    case '01':
        http_response_code(301);
        header('Location: ../list/shelf.php?id=' . $code_content);
        exit;

    case '02':
        http_response_code(301);
        header('Location: ../list/case.php?id=' . $code_content);
        exit;

    case '03':
        http_response_code(301);
        header('Location: ../list/room.php?id=' . $code_content);
        exit;

    case '04':
        // User. ToDo.
        http_response_code(400);
        die('User code is not supported yet.');
        break;

    default:
        http_response_code(400);
        die('Invalid Code');
}