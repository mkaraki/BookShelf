<?php
require __DIR__ . '/../internal/lib_util.php';
require __DIR__ . '/../internal/login_info.php';
global $login_is;
$focus_jump = true;
require __DIR__ . '/../partial/page-head.php';
require_once __DIR__ . '/../partial/shelf-list.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    die('No site id specified');

$site = get_site($_GET['id']);
if (!$site)
    die('Site not found');
?>

<section>
    <h1>Site: <?= htmlentities($site['siteName']) ?></h1>
</section>

<section>
    <?php if ($login_is) : ?>
        <form action="create_room.php" class="mb-3" method="POST">
            <div class="mb-3">
                <label for="name" class="from-label">Room Name</label>
                <input type="text" name="name" id="name" maxlength="256" required class="form-control">
                <input type="hidden" name="p" value="<?= $_GET['id'] ?>">
            </div>
            <button type="submit" class="btn btn-primary">Create Room</button>
        </form>
    <?php endif; ?>
</section>

<section>
    <h2>Rooms:</h2>
    <ul>
        <?php foreach (get_bookStoreTreeWithSite($_GET['id'])['rooms'] as $room) { htmlRoom($room); } ?>
    </ul>
</section>

<?php require __DIR__ . '/../partial/page-end.php'; ?>