function adminCatalogEscapeHtml(value) {
    return String(value === null || value === undefined ? '' : value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function adminCatalogSetMessage(message, isError) {
    var target = document.getElementById('catalogMessage');
    if (!target) {
        if (message) {
            alert(message);
        }
        return;
    }

    target.textContent = message || '';
    target.style.color = isError ? '#b91c1c' : '#166534';
}

function adminCatalogRow(book) {
    return '<tr>' +
        '<td>' + adminCatalogEscapeHtml(book.title) + '</td>' +
        '<td>' + adminCatalogEscapeHtml(book.author) + '</td>' +
        '<td>' + adminCatalogEscapeHtml(book.genre_name || 'None') + '</td>' +
        '<td>' + adminCatalogEscapeHtml(book.isbn) + '</td>' +
        '<td>' + adminCatalogEscapeHtml(book.total_stock || 0) + '</td>' +
        '<td>' + adminCatalogEscapeHtml(book.total_available || 0) + '</td>' +
        '<td>' +
            '<form method="POST" action="../../Controllers/AdminBookFormController.php" style="display:inline;">' +
                '<input type="hidden" name="id" value="' + adminCatalogEscapeHtml(book.id) + '">' +
                '<button type="submit" class="btn-link">Edit Details</button>' +
            '</form> ' +
            '<form method="POST" action="../../Controllers/AdminBookActionController.php" class="book-delete-form" style="display:inline;">' +
                '<input type="hidden" name="action" value="delete">' +
                '<input type="hidden" name="id" value="' + adminCatalogEscapeHtml(book.id) + '">' +
                '<button type="submit" class="btn-link-danger">Delete</button>' +
            '</form>' +
        '</td>' +
    '</tr>';
}

function adminCatalogRenderBooks(books) {
    var body = document.getElementById('catalogTableBody');
    if (!body) return;

    var html = '';
    if (!Array.isArray(books) || books.length === 0) {
        html = '<tr><td colspan="7">No books found in the global catalog.</td></tr>';
    } else {
        for (var i = 0; i < books.length; i++) {
            html += adminCatalogRow(books[i]);
        }
    }

    body.innerHTML = html;
}

function adminCatalogFetch(form) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function () {
        var data;
        try {
            data = JSON.parse(xhr.responseText || '{}');
        } catch (error) {
            adminCatalogSetMessage('Invalid JSON response.', true);
            return;
        }

        if (xhr.status < 200 || xhr.status >= 300 || (data && data.success === false)) {
            adminCatalogSetMessage((data && (data.message || data.error)) ? (data.message || data.error) : 'Search failed.', true);
            return;
        }

        adminCatalogRenderBooks((data && data.books) ? data.books : []);
        adminCatalogSetMessage(data.message || 'Catalog loaded.', false);
    };
    xhr.onerror = function () {
        adminCatalogSetMessage('Search failed.', true);
    };
    xhr.send(new FormData(form));
}

function adminCatalogDelete(form) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function () {
        var data;
        try {
            data = JSON.parse(xhr.responseText || '{}');
        } catch (error) {
            adminCatalogSetMessage('Invalid JSON response.', true);
            return;
        }

        if (xhr.status < 200 || xhr.status >= 300 || (data && data.success === false)) {
            adminCatalogSetMessage((data && (data.message || data.error)) ? (data.message || data.error) : 'Delete failed.', true);
            return;
        }

        adminCatalogSetMessage(data.message || 'Book deleted.', false);
        adminCatalogFetch(document.getElementById('catalogSearchForm'));
    };
    xhr.onerror = function () {
        adminCatalogSetMessage('Delete failed.', true);
    };
    xhr.send(new FormData(form));
}

document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!form || form.tagName !== 'FORM') {
        return;
    }

    if (form.id === 'catalogSearchForm') {
        event.preventDefault();
        adminCatalogFetch(form);
        return;
    }

    if (form.classList && form.classList.contains('book-delete-form')) {
        event.preventDefault();
        if (window.confirm('Permanently delete this book from the global catalog? This action cannot be undone.')) {
            adminCatalogDelete(form);
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    var searchForm = document.getElementById('catalogSearchForm');
    var initialBooks = window.adminCatalogInitialBooks || [];

    adminCatalogRenderBooks(initialBooks);

    if (searchForm && searchForm.elements && searchForm.elements.search) {
        searchForm.elements.search.value = window.adminCatalogInitialSearch || '';
    }
});