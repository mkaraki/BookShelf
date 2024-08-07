function addAuthorNum(preDefinedAuthor = '', preDefinedAuthorId = null) {
    const authorNum = parseInt(document.getElementById('authorNum').value);
    const newAuthorNum = authorNum + 1;
    document.getElementById('authorNum').value = newAuthorNum;
    const authorHolder = document.getElementById('authorHolder');

    const newAuthorHolder = document.createElement('div');

    const newAuthor = document.createElement('input');
    newAuthor.setAttribute('type', 'text');
    newAuthor.setAttribute('name', `author${newAuthorNum}`);
    newAuthor.setAttribute('id', `author${newAuthorNum}`);
    newAuthor.setAttribute('class', 'form-control');
    newAuthor.setAttribute('value', preDefinedAuthor);
    if (preDefinedAuthor !== null)
        newAuthor.setAttribute('readonly', '1');
    newAuthorHolder.appendChild(newAuthor);

    const internalAuthorId = document.createElement('input');
    internalAuthorId.setAttribute('type', 'hidden');
    internalAuthorId.setAttribute('name', `internalAuthorId${newAuthorNum}`);
    internalAuthorId.setAttribute('id', `internalAuthorId${newAuthorNum}`);
    internalAuthorId.setAttribute('value', preDefinedAuthorId ?? '');
    newAuthorHolder.appendChild(internalAuthorId);

    const newAuthorSearch = document.createElement('button');
    newAuthorSearch.setAttribute('type', 'button');
    newAuthorSearch.setAttribute('onclick', `searchAuthor(${newAuthorNum})`);
    newAuthorSearch.setAttribute('class', 'btn btn-primary btn-sm m-1');
    newAuthorSearch.innerText = '🔎';
    newAuthorHolder.appendChild(newAuthorSearch);

    const clearAuthor = document.createElement('button');
    clearAuthor.setAttribute('type', 'button');
    clearAuthor.setAttribute('onclick', `clearAuthor(${newAuthorNum})`);
    clearAuthor.setAttribute('class', 'btn btn-danger btn-sm m-1');
    clearAuthor.innerText = '×';
    newAuthorHolder.appendChild(clearAuthor);

    authorHolder.appendChild(newAuthorHolder);
}

function clearAuthor(num) {
    document.getElementById(`author${num}`).value = '';
    document.getElementById(`internalAuthorId${num}`).value = '';
    document.getElementById(`author${num}`).removeAttribute('readonly');
}

function searchAuthor(num) {
    const name = document.getElementById(`author${num}`).value;
    const ue_name = encodeURI(name);
    const neww = window.open(`search_author.php?name=${ue_name}&num=${num}`, '_blank', 'width=800,height=600');
    neww.focus();
}

function setAuthorId(num, id) {
    document.getElementById(`internalAuthorId${num}`).value = id;
    document.getElementById(`author${num}`).setAttribute('readonly', '1');
    return true;
}

function clearPublisher() {
    document.getElementById('publisher').value = '';
    document.getElementById('internalPublisherId').value = '';
    document.getElementById('publisher').removeAttribute('readonly');
}

function searchPublisher() {
    const name = document.getElementById('publisher').value;
    const ue_name = encodeURI(name);
    const neww = window.open(`search_publisher.php?name=${ue_name}`, '_blank', 'width=800,height=600');
    neww.focus();
}

function setPublisherId(id) {
    document.getElementById('internalPublisherId').value = id;
    document.getElementById('publisher').setAttribute('readonly', '1');
    return true;
}

function putSearchedAuthorData(authors, codes) {
    for (let i = 0; i < authors.length; i++) {
        addAuthorNum(authors[i], codes[i]);
    }
}

function putSearchedPublisherData(publisher, code = null) {
    document.getElementById('publisher').value = publisher;
    if (code !== null) {
        document.getElementById('internalPublisherId').value = code;
        document.getElementById('publisher').setAttribute('readonly', '1');
    }
}

function putSearchedData(name, nameRead, isbn = '') {
    document.getElementById('name').value = name;
    document.getElementById('name_read').value = nameRead;
    document.getElementById('isbn').value = isbn;
}

function searchIsbnNDL(isbn) {
    const url = `/search/proxy/jp_ndl.php?isbn=${isbn}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.length < 1) {
                alert('No data');
                return;
            }

            const d = data[0];
            const title = d.title;
            const titleRead = d.titleRead;
            const publisher = d.publisher;
            postSearch(title, titleRead, [], publisher, isbn);
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
            postSearch(title, '', authors, '', isbn);
        });
}

function postSearch(title, titleRead, authors, publisher, isbn) {
    const f = new FormData();
    f.set('isbn', isbn)
    f.set('publisher', publisher)
    f.set('authornum', authors.length)
    for (let i = 0; i < authors.length; i++) {
        f.set('author' + (i + 1), authors[i]);
    }

    console.log(f);

    fetch('isbn_post.php', {
        method: 'POST',
        body: f
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.isbn)
                if (data.isbn.length > 0) {
                    alert('Same ISBN already exists.');
                }

            if (data.publisher)
                if (data.publisher.length > 0)
                    putSearchedPublisherData(data.publisher[0].publisherName, data.publisher[0].publisherId);

            if (data.authors)
                for (let i = 0; i < authors.length; i++) {
                    if (data.authors[i].length < 1)
                        putSearchedAuthorData([authors[i]], ['']);
                    else
                        putSearchedAuthorData([data.authors[i][0].authorName], [data.authors[i][0].authorId]);
                }

            putSearchedData(title, titleRead, isbn);
        });
}

function searchIsbn() {
    searchIsbnNDL(document.getElementById('sisbn').value);
    //searchIsbnGoogleBooks(document.getElementById('sisbn').value);

    document.getElementById('sisbn').value = '';
}

window.onload = () => {
    document.getElementById('sisbn').focus();
};