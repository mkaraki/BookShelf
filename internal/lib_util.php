<?php
require_once __DIR__ . '/db.php';

function get_sites()
{
    return DB::query('SELECT * FROM siteInfo');
}
