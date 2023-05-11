<?php require __DIR__ . '/internal/lib_util.php'; ?>
<?php //require __DIR__ . '/internal/auth.php'; 
?>
<?php require __DIR__ . '/partial/page-head.php'; ?>

<style>
    .err {
        color: red;
    }
</style>

<section>
    Hello <?= $login_name ?>.
    <?php if (!$login_is) : ?>
        <a href="dash.php">Login</a>
    <?php endif; ?>
</section>

<section>
    <form action="" method="get" onsubmit="return false">
        <label for="sisbn">ISBN</label>
        <input type="text" id="sisbn">
        <button type="submit" onclick="searchIsbn();">Search</button>
    </form>
</section>

<form action="add_book.php" method="POST">
    <section>
        <h3>Title</h3>
        <label for="name">Book title</label>
        <input type="text" name="name" id="name" required maxlength="512">
        <br />
        <label for="name_read">Name Read</label>
        <input type="text" name="name_read" id="name_read" maxlength="512">

        <br /><br />

        <label for="disambiguation">Disambiguation</label>
        <input type="text" name="disambiguation" id="disambiguation" maxlength="512">
    </section>

    <section>
        <h3>Authors</h3>
        <div id="authorHolder">
            <input type="hidden" name="authorNum" id="authorNum">
        </div>
        <br />
        <button type="button" onclick="addAuthorNum()">New</button>
        <span class="err">Non commited author will ignore when insert to database.</span>
    </section>

    <section>
        <h3>Publisher</h3>
        <label for="publisher">Publisher</label>
        <input type="text" name="publisher" id="publisher">
        <button type="button" onclick="clearPublisher()">×</button>
        <button type="button" onclick="searchPublisher()">🔎</button>
        <br />

        <span class="err">Non commited publisher will ignore when insert to database.</span>
    </section>

    <section>
        <h3>Metadata</h3>
        <label for="isbn">ISBN</label>
        <input type="text" name="isbn" id="isbn" maxlength="13">
    </section>

    <br />

    <input type="submit" value="Add Book">
</form>


<script src="add_book.js"></script>
<?php require __DIR__ . '/partial/page-end.php'; ?>