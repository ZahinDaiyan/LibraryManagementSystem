function adminAjaxMessage(message, isError) {
    var target = document.getElementById('adminAjaxMessage');
    if (!target) {
        if (message) {
            alert(message);
        }
        return;
    }

    target.textContent = message || '';
    target.style.color = isError ? '#b91c1c' : '#166534';
}

function adminAjaxSubmit(form) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.onload = function () {
        var data;
        try {
            data = JSON.parse(xhr.responseText || '{}');
        } catch (error) {
            adminAjaxMessage('Invalid JSON response.', true);
            return;
        }

        if (xhr.status < 200 || xhr.status >= 300 || (data && data.success === false)) {
            adminAjaxMessage((data && (data.message || data.error)) ? (data.message || data.error) : 'Request failed.', true);
            return;
        }

        adminAjaxMessage(data.message || 'Saved.', false);
        if (data.redirect) {
            window.location.href = data.redirect;
            return;
        }

        if (window.location.reload) {
            window.location.reload();
        }
    };
    xhr.onerror = function () {
        adminAjaxMessage('Request failed.', true);
    };
    xhr.send(new FormData(form));
}

document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!form || form.tagName !== 'FORM') {
        return;
    }

    var action = form.getAttribute('action') || '';
    var isAdminController = action.indexOf('/Controllers/Admin') !== -1 || action.indexOf('Controllers/Admin') !== -1;
    var isRenewalDecision = action.indexOf('RenewalDecisionController.php') !== -1;
    var isAjaxSearch = form.id === 'catalogSearchForm';
    var isExplicitAjax = form.getAttribute('data-admin-ajax') === '1';

    if ((!isAdminController && !isRenewalDecision) || isAjaxSearch || !isExplicitAjax) {
        return;
    }

    event.preventDefault();
    adminAjaxSubmit(form);
});
