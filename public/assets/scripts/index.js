function callCodeReaderForBookSearchScreen() {
    window.applyCodeReaderResult = function (s) {
        setTimeout(function() {
            document.getElementById("search-book-isbn").value = s;
            document.getElementById("search-book-isbn-form").submit();
        }, 0)
    }
    callCodeReader();
}

function callCodeReaderForCodeJumpScreen() {
    window.applyCodeReaderResult = function (s) {
        setTimeout(function() {
            document.getElementById("jump-to-code").value = s;
            document.getElementById("jump-to-code-form").submit();
        }, 0)
    }
    callCodeReader();
}
