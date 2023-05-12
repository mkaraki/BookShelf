<?php require __DIR__ . '/../internal/lib_util.php'; ?>
<?php require __DIR__ . '/../internal/auth.php'; ?>
<?php
$page_title = 'Add book - Book Shelf';
?>
<?php require __DIR__ . '/../partial/page-head.php'; ?>

<style>
    .err {
        color: red;
    }

    input[readonly] {
        background-color: #0f0;
    }
</style>

<h1>Add books</h1>

<section>
    <form action="" method="get" onsubmit="return false" class="mb-4">
        <div class="mb-3">
            <label for="sisbn" class="form-label">ISBN</label>
            <input type="text" id="sisbn" class="form-control form-control-sm">
        </div>
        <button type="submit" onclick="searchIsbn();" class="btn btn-primary">Search</button>
    </form>
</section>

<form action="add_book.php" method="POST">
    <input type="hidden" name="shelfId" value="<?= htmlentities($_GET['id']) ?>">

    <section>
        <h3>Title</h3>
        <div class="mb-3">
            <label for="name" class="form-label">Book title</label>
            <input type="text" name="name" id="name" required maxlength="512" class="form-control">
        </div>
        <div class="mb-3">
            <label for="name_read" class="form-label">Name Read</label>
            <input type="text" name="name_read" id="name_read" maxlength="512" class="form-control form-control-sm">
        </div>

        <div class="mb-3">
            <label for="disambiguation" class="form-label">Disambiguation</label>
            <input type="text" name="disambiguation" id="disambiguation" maxlength="512" class="form-control form-control-sm">
        </div>
    </section>

    <section class="mb-3">
        <h3>Authors</h3>
        <div id="authorHolder">
            <input type="hidden" name="authorNum" id="authorNum" value="0">
        </div>
        <button type="button" onclick="addAuthorNum()" class="btn btn-primary btn-sm">New</button>
        <span class="err">Non commited author will ignore when insert to database.</span>
    </section>

    <section>
        <h3>Publisher</h3>
        <label for="publisher" class="form-label">Publisher</label>
        <input type="text" name="publisher" id="publisher" class="form-control">
        <input type="hidden" name="internalPublisherId" id="internalPublisherId">
        <button type="button" onclick="searchPublisher()" class="btn btn-primary btn-sm m-1">🔎</button>
        <button type="button" onclick="clearPublisher()" class="btn btn-danger btn-sm m-1">×</button>

        <span class="err">Non commited publisher will ignore when insert to database.</span>
    </section>

    <section class="mb-3">
        <h3>Metadata</h3>
        <label for="isbn" class="form-label">ISBN</label>
        <input type="text" name="isbn" id="isbn" maxlength="13" class="form-control form-control-sm">
    </section>

    <input type="submit" value="Add Book" class="btn btn-primary">
</form>


<script src="add_book.js"></script>
<?php require __DIR__ . '/../partial/page-end.php'; ?>