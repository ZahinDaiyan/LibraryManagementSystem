function librarianOperationsEscapeHtml(value) {
    return String(value === null || value === undefined ? '' : value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function librarianOperationsSetMessage(message, isError) {
    var target = document.getElementById('librarianOpsMessage');
    if (!target) {
        if (message) {
            alert(message);
        }
        return;
    }

    target.textContent = message || '';
    target.style.color = isError ? '#b91c1c' : '#166534';
}

function librarianOperationsRenderRows(rows, renderRow, emptyMessage, colspan) {
    if (!Array.isArray(rows) || rows.length === 0) {
        return '<tr><td colspan="' + (colspan || 6) + '">' + librarianOperationsEscapeHtml(emptyMessage) + '</td></tr>';
    }

    var html = '';
    for (var i = 0; i < rows.length; i++) {
        html += renderRow(rows[i]);
    }

    return html;
}

function librarianOperationsRenderActiveLoans(rows) {
    var body = document.getElementById('activeLoansBody');
    if (!body) return;

    body.innerHTML = librarianOperationsRenderRows(rows, function (loan) {
        return '<tr>' +
            '<td>' + librarianOperationsEscapeHtml(loan.member_id) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(loan.id) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(loan.member_name) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(loan.book_title) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(loan.borrow_date) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(loan.due_date) + '</td>' +
        '</tr>';
    }, 'No active loans found.', 6);
}

function librarianOperationsRenderMemberHistory(rows) {
    var body = document.getElementById('memberHistoryBody');
    if (!body) return;

    body.innerHTML = librarianOperationsRenderRows(rows, function (history) {
        return '<tr>' +
            '<td>' + librarianOperationsEscapeHtml(history.id) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(history.title) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(history.status) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(history.borrow_date) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(history.due_date) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(history.return_date) + '</td>' +
        '</tr>';
    }, 'No loan history loaded.', 6);
}

function librarianOperationsRenderMemberFines(rows) {
    var body = document.getElementById('memberFinesBody');
    if (!body) return;

    body.innerHTML = librarianOperationsRenderRows(rows, function (fine) {
        return '<tr>' +
            '<td>' + librarianOperationsEscapeHtml(fine.id) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(fine.amount) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(fine.reason) + '</td>' +
            '<td>' + librarianOperationsEscapeHtml(fine.is_paid) + '</td>' +
        '</tr>';
    }, 'No fine history loaded.', 4);
}

function librarianOperationsIsAjaxForm(form) {
    var action = form.getAttribute('action') || '';
    return action.indexOf('LibrarianOperationsActionController.php') !== -1 || action.indexOf('LibrarianOperationsController.php') !== -1;
}

function librarianOperationsSubmitForm(form) {
    var action = form.getAttribute('action') || '';
    var isPageController = action.indexOf('LibrarianOperationsController.php') !== -1;
    var hasMemberId = !!(form.elements && form.elements.namedItem && form.elements.namedItem('member_id') && String(form.elements.namedItem('member_id').value || '').trim() !== '');
    var hasLoanFilter = !!(form.elements && form.elements.namedItem && form.elements.namedItem('loan_filter'));
    var xhr = new XMLHttpRequest();

    xhr.open((form.method || 'POST').toUpperCase(), action, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function () {
        var data;
        try {
            data = JSON.parse(xhr.responseText || '{}');
        } catch (error) {
            librarianOperationsSetMessage('Invalid JSON response.', true);
            return;
        }

        if (xhr.status < 200 || xhr.status >= 300 || (data && data.success === false)) {
            librarianOperationsSetMessage((data && (data.message || data.error)) ? (data.message || data.error) : 'Request failed.', true);
            return;
        }

        if (isPageController) {
            if (data && data.data) {
                if (hasLoanFilter && Object.prototype.hasOwnProperty.call(data.data, 'active_loans')) {
                    librarianOperationsRenderActiveLoans(data.data.active_loans || []);
                }
                if (hasMemberId && Object.prototype.hasOwnProperty.call(data.data, 'member_history')) {
                    librarianOperationsRenderMemberHistory(data.data.member_history || []);
                }
                if (hasMemberId && Object.prototype.hasOwnProperty.call(data.data, 'member_fines')) {
                    librarianOperationsRenderMemberFines(data.data.member_fines || []);
                }
            }

            librarianOperationsSetMessage(data.message || 'Updated.', false);
            return;
        }

        librarianOperationsSetMessage(data.message || 'Updated.', false);
        window.location.reload();
    };
    xhr.onerror = function () {
        librarianOperationsSetMessage('Request failed.', true);
    };
    xhr.send(new FormData(form));
}

document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!form || form.tagName !== 'FORM') {
        return;
    }

    if (!librarianOperationsIsAjaxForm(form)) {
        return;
    }

    event.preventDefault();
    librarianOperationsSubmitForm(form);
});