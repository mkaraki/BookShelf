<?php

$isbn = $_GET['isbn'];
if (!is_numeric($isbn)) {
    die('No isbn specified');
}

$url = "https://iss.ndl.go.jp/api/opensearch?isbn=$isbn&cnt=1";
$data = curl_exec(curl_init($url));

print($data);
