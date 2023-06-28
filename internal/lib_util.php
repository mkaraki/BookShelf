<?php
require_once __DIR__ . '/db.php';

function get_sites()
{
    return DB::query('SELECT * FROM siteInfo');
}

function get_site($siteId)
{
    return DB::queryFirstRow('SELECT * FROM siteInfo WHERE siteId = %d', $siteId);
}

function get_rooms($siteId)
{
    return DB::query('SELECT * FROM roomInfo WHERE parentSite = %d', $siteId);
}

function get_room($roomId)
{
    return DB::queryFirstRow('SELECT * FROM roomInfo WHERE roomId = %d', $roomId);
}

function get_cases($roomId)
{
    return DB::query('SELECT * FROM caseInfo WHERE parentRoom = %d', $roomId);
}

function get_case($caseId)
{
    return DB::queryFirstRow('SELECT * FROM caseInfo WHERE caseId = %d', $caseId);
}

function get_shelfs($caseId)
{
    return DB::query('SELECT * FROM shelfInfo WHERE parentCase = %d ORDER BY shelfNumber', $caseId);
}

function get_shelf($shelfId)
{
    return DB::queryFirstRow('SELECT * FROM shelfInfo WHERE shelfId = %d', $shelfId);
}

function get_books($shelfId)
{
    return DB::query('SELECT * FROM bookCollection WHERE belongShelf = %d', $shelfId);
}

function get_publisher($publisherId)
{
    return DB::queryFirstRow('SELECT * FROM publisherInfo WHERE publisherId = %d', $publisherId);
}

function get_authors($bookId)
{
    $links = DB::query('SELECT * FROM bookAuthorLinker WHERE uniqueBookId = %d', $bookId);
    $authors = [];
    foreach ($links as $link) {
        $authors[] = DB::queryFirstRow('SELECT * FROM authorInfo WHERE authorId = %d', $link['authorId']);
    }
    return $authors;
}

function search_authors($name)
{
    return DB::query('SELECT * FROM authorInfo WHERE authorName LIKE %ss', $name);
}

function search_publishers($name)
{
    return DB::query('SELECT * FROM publisherInfo WHERE publisherName LIKE %ss', $name);
}

function search_book_isbn($isbn)
{
    return DB::query(
        'SELECT b.uniqueBookId, b.bookName, b.bookRead, b.isbn, b.bookDisambiguation, sl.shelfNumber, c.caseName, r.roomName, s.siteName
        FROM bookCollection b, shelfInfo sl, caseInfo c, roomInfo r, siteInfo s
        WHERE isbn = %d AND
        b.belongShelf = sl.shelfId AND
        sl.parentCase = c.caseId AND
        c.parentRoom = r.roomId AND
        r.parentSite = s.siteId',
        $isbn
    );
}

function calc_bcd_cd($code)
{
    $code = str_split(strval($code));
    $sum = 0;
    foreach ($code as $c) {
        if (!is_numeric($c))
            return false;

        $c = intval($c);
        $sum += $c;
        $sum = $sum % 10;
    }

    return $sum % 10;
}
