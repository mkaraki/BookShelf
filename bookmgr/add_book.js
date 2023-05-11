function addAuthorNum(preDefinedAuthor = '') {
    const authorNum = parseInt(document.getElementById('authorNum').value);
    const newAuthorNum = authorNum + 1;
    document.getElementById('authorNum').innerText = newAuthorNum;
    const authorHolder = document.getElementById('authorHolder');

    const newAuthorHolder = document.createElement('div');

    const newAuthor = document.createElement('input');
    newAuthor.setAttribute('type', 'text');
    newAuthor.setAttribute('name', `author${newAuthorNum}`);
    newAuthor.setAttribute('id', `author${newAuthorNum}`);
    newAuthor.setAttribute('class', 'form-control');
    newAuthor.setAttribute('placeholder', '著者名');
    newAuthor.setAttribute('value', preDefinedAuthor);
    newAuthorHolder.appendChild(newAuthor);

    const internalAuthorId = document.createElement('input');
    internalAuthorId.setAttribute('type', 'hidden');
    internalAuthorId.setAttribute('name', `internalAuthorId${newAuthorNum}`);
    internalAuthorId.setAttribute('id', `internalAuthorId${newAuthorNum}`);
    internalAuthorId.setAttribute('value', '');
    newAuthorHolder.appendChild(internalAuthorId);

    const clearAuthor = document.createElement('button');
    clearAuthor.setAttribute('type', 'button');
    clearAuthor.setAttribute('onclick', `clearAuthor(${newAuthorNum})`);
    clearAuthor.innerText = '×';
    newAuthorHolder.appendChild(clearAuthor);

    const newAuthorSearch = document.createElement('button');
    newAuthorSearch.setAttribute('type', 'button');
    newAuthorSearch.setAttribute('onclick', `searchAuthor(${newAuthorNum})`);
    newAuthorSearch.innerText = '🔎';
    newAuthorHolder.appendChild(newAuthorSearch);

    authorHolder.appendChild(newAuthorHolder);
}

function putSearchedData(name, nameRead, authors, publisher, isbn = '') {
    for (let i = 0; i < authors.length; i++) {
        addAuthorNum(authors[i]);
    }
    document.getElementById('name').value = name;
    document.getElementById('name_read').value = nameRead;
    document.getElementById('publisher').value = publisher;
    document.getElementById('isbn').value = isbn;
}

function searchIsbnNDL(isbn) {
    const url = `https://iss.ndl.go.jp/api/opensearch?isbn=${isbn}&cnt=1`;
    fetch(url, {
        mode: 'no-cors'
    })
        .then(response => response.text())
        .then(text => new window.DOMParser().parseFromString(text, "text/xml"))
        .then(data => {
            let records = data.documentElement.getElementsByTagName('item');
            if (records.length < 1) {
                alert('No data');
                return;
            }

            records = records[0];
            const title = records.getElementsByTagName('title')[0].textContent;
            const titleRead = records.getElementsByTagName('dcndl:titleTranscription')[0].textContent;
            const authorE = records.getElementsByTagName('author');
            let author = '';
            if (authorE.length > 1)
                author = [0].textContent;

            const publisher = records.getElementsByTagName('dc:publisher')[0].textContent;

            putSearchedData(title, titleRead, [author], publisher, isbn)
        });
}

function searchIsbnGoogleBooks(isbn) {
    const url = `https://www.googleapis.com/books/v1/volumes?q=isbn:${isbn}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.items.length < 1) {
                alert('No data');
                return;
            }

            const d = data.items[0].volumeInfo;
            const title = d.title;
            const authors = d.authors;
            putSearchedData(title, '', authors, '', isbn);
        });
}

function searchIsbn() {
    //searchIsbnNDL(document.getElementById('sisbn').value);
    searchIsbnGoogleBooks(document.getElementById('sisbn').value);

    document.getElementById('sisbn').value = '';
}

window.onload = () => {
    document.getElementById('sisbn').focus();
};