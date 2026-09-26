function searchIsbnAndApplyToBook()
{
    const isbnSearchElem = document.getElementById('search_isbn');
    if (isbnSearchElem === null) return;

    const searchIsbn = isbnSearchElem.value
        .trim()
        .replace(/-/g, '')
        .replace(/\s/g, '')
    ;
    if (searchIsbn.match(/^[0-9]{13}$/) === null) {
        alert('Invalid ISBN');
        return;
    }

    const url = `/search/proxy/jp_ndl?isbn=${searchIsbn}`;
    fetch(url, {
        credentials: "include"
    })
        .then(response => response.json())
        .then(data => {
            if (data.length < 1) {
                alert('No data');
                return;
            }

            const d = data[0];

            document.getElementById('book_name').value = d.title ?? '';
            document.getElementById('book_bookRead').value = d.titleRead ?? '';
            document.getElementById('book_isbn').value = searchIsbn ?? '';

            const pubElem = document.getElementById('book_publisher');
            Array.from(pubElem.getElementsByTagName('option')).forEach(function (e) {
                console.log(d.publisher, e.innerText.trim());
                if (d.publisher === e.innerText.trim()) {
                    pubElem.value = e.value;
                }
            })
        })
        .catch((e) => {
            alert('Error occurred while fetching data');
            console.error('Error occurred while fetching data', e);
        });
}

function applyIsbnCode(code) {
    setTimeout(function() {
        document.getElementById('search_isbn').value = code;
        searchIsbnAndApplyToBook();
    }, 0);
}

window.applyCodeReaderResult = applyIsbnCode;
