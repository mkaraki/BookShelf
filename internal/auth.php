<?php
require_once __DIR__ . '/login_info.php';

if ($login_is !== true) {
    header('WWW-Authenticate: Basic realm="You need to login"');
    header('HTTP/1.0 401 Unauthorized');
    die('You need to login');
} else {
    
}
