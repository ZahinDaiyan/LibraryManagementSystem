function memberAjaxResolveRedirect(redirect) {
    if (!redirect) {
        return '';
    }

    if (/^https?:\/\//i.test(redirect) || redirect.indexOf('/LibraryManagementSystem/') === 0) {
        return redirect;
    }

    if (redirect.indexOf('../') === 0 || redirect.indexOf('./') === 0 || redirect.indexOf('/') === 0) {
        return redirect;
    }

    return '/LibraryManagementSystem/Controllers/' + redirect;
}

function memberAjaxSupportsAction(action) {
    if (!action) {
        return false;
    }

    var path = action.toLowerCase();
    return path.indexOf('bookreviewcontroller.php') !== -1
        || path.indexOf('borrowrequestcontroller.php') !== -1
        || path.indexOf('fineactioncontroller.php') !== -1
        || path.indexOf('loanactioncontroller.php') !== -1
        || path.indexOf('notificationactioncontroller.php') !== -1
        || path.indexOf('profileupdatecontroller.php') !== -1
        || path.indexOf('readinglistactioncontroller.php') !== -1
        || path.indexOf('reservationactioncontroller.php') !== -1
        || path.indexOf('membercomplaintactioncontroller.php') !== -1;
}

function memberAjaxSubmitForm(form) {
    var xhr = new XMLHttpRequest();
    xhr.open((form.method || 'POST').toUpperCase(), form.action, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function () {
        var payload;

        try {
            payload = JSON.parse(xhr.responseText || '{}');
        } catch (error) {
            alert('Invalid JSON response.');
            return;
        }

        if (xhr.status < 200 || xhr.status >= 300 || (payload && payload.success === false)) {
            alert((payload && (payload.message || payload.error)) ? (payload.message || payload.error) : 'Request failed.');
            return;
        }

        if (payload && payload.message) {
            alert(payload.message);
        }

        var resolvedRedirect = memberAjaxResolveRedirect(payload && payload.redirect ? payload.redirect : '');
        if (resolvedRedirect) {
            window.location.href = resolvedRedirect;
            return;
        }

        window.location.reload();
    };

    xhr.onerror = function () {
        alert('Request failed.');
    };

    xhr.send(new FormData(form));
}

document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!form || form.tagName !== 'FORM') {
        return;
    }

    if (!memberAjaxSupportsAction(form.action || '')) {
        return;
    }

    event.preventDefault();
    memberAjaxSubmitForm(form);
});
