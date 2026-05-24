function showHint(str) {
    var txt = document.getElementById('txtHint');
    if (!txt) return;
    if (str.length === 0) {
        txt.innerHTML = '';
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        if (xhttp.status === 200) {
            var data;
            try { data = JSON.parse(xhttp.responseText); } catch (e) { console.error('Invalid JSON', e); return; }
            var out = '';
            if (!Array.isArray(data) || data.length === 0) {
                out = '';
            } else {
                data.forEach(function(item) {
                    out += '<div style="padding:6px;cursor:pointer;color:#e8eaf0;background:#1a1d2e;margin-bottom:6px;border-radius:6px;" onclick="selectMemberSuggestion(\'' + (item.name ? item.name.replace(/'/g, "\\'") : '') + '\')">';
                    out += '<strong style="display:block;color:#f0f2ff">' + (item.name || '') + '</strong>';
                    out += '<small style="color:#9a9fbf">' + (item.email || '') + ' · ' + (item.phone || '') + '</small>';
                    out += '</div>';
                });
            }
            txt.innerHTML = out;
        } else {
            console.error('Request failed', xhttp.status);
        }
    };
    xhttp.open('POST', '../../Controllers/gethint.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhttp.send('q=' + encodeURIComponent(str));
}

function searchMembersAjax(str) {
    var tbody = document.getElementById('memberResultsBody');
    if (!tbody) return;

    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        if (xhttp.status === 200) {
            var data;
            try { data = JSON.parse(xhttp.responseText); } catch (e) { console.error('Invalid JSON', e); return; }
            var display = '';
            if (data && data.error) {
                display = "<tr><td colspan='4'>" + (data.error || 'Error') + "</td></tr>";
            } else if (!Array.isArray(data) || data.length === 0) {
                display = "<tr><td colspan='4'>No members found.</td></tr>";
            } else {
                for (var i = 0; i < data.length; i++) {
                    var m = data[i];
                    display += '<tr>';
                    display += '<td>' + (m.name || '') + '</td>';
                    display += '<td>' + (m.email || '') + '</td>';
                    display += '<td>' + (m.phone || '') + '</td>';
                    display += '<td>';
                    display += '<form action="../../Controllers/LibrarianOperationsController.php" method="POST" style="display:inline;">';
                    display += '<input type="hidden" name="member_id" value="' + (m.id || '') + '">';
                    display += '<button type="submit">View History</button>';
                    display += '</form>';
                    display += '</td>';
                    display += '</tr>';
                }
            }
            tbody.innerHTML = display;
        } else {
            console.error('Request failed', xhttp.status);
        }
    };
    xhttp.open('POST', '../../Controllers/MemberSearchResultsApi.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhttp.send('q=' + encodeURIComponent(str));
}

function searchReturnsAjax(str) {
    var tbody = document.getElementById('returnResultsBody');
    if (!tbody) return;

    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        if (xhttp.status === 200) {
            var data;
            try { data = JSON.parse(xhttp.responseText); } catch (e) { console.error('Invalid JSON', e); return; }
            var display = '';
            if (data && data.error) {
                display = "<tr><td colspan='6'>" + (data.error || 'Error') + "</td></tr>";
            } else if (!Array.isArray(data) || data.length === 0) {
                display = "<tr><td colspan='6'>No records found.</td></tr>";
            } else {
                for (var i = 0; i < data.length; i++) {
                    var r = data[i];
                    display += '<tr>';
                    display += '<td>' + (r.id || '') + '</td>';
                    display += '<td>' + (r.member_name || '') + '</td>';
                    display += '<td>' + (r.book_title || '') + '</td>';
                    display += '<td>' + (r.status || '') + '</td>';
                    display += '<td>' + (r.due_date || '') + '</td>';
                    display += '<td>';
                    display += '<form novalidate action="../../Controllers/LibrarianOperationsActionController.php" method="POST">';
                    display += '<input type="hidden" name="action" value="process_return">';
                    display += '<input type="hidden" name="borrow_record_id" value="' + (r.id || '') + '">';
                    display += '<button type="submit">Mark Returned</button>';
                    display += '</form>';
                    display += '</td>';
                    display += '</tr>';
                }
            }
            tbody.innerHTML = display;
        } else {
            console.error('Request failed', xhttp.status);
        }
    };
    xhttp.open('POST', '../../Controllers/ReturnSearchApi.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhttp.send('q=' + encodeURIComponent(str));
}
