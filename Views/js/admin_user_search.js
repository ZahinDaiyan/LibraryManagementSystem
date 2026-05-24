function ajaxSearchUsers() {
    const searchVal = document.getElementById("userSearch").value;
    const roleVal = document.getElementById("userRole").value;
    const tbody = document.getElementById("userTableBody");

    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        if (xhttp.status === 200) {
            const users = JSON.parse(xhttp.responseText);
            let display = "";
            if (users.length === 0) {
                display = "<tr><td colspan='7'>No users found matching your criteria.</td></tr>";
            } else {
                for (let i = 0; i < users.length; i++) {
                    const u = users[i];
                    
                    const selectedMember = (u.role === 'member') ? 'selected' : '';
                    const selectedLibrarian = (u.role === 'librarian') ? 'selected' : '';
                    const selectedManager = (u.role === 'branch_manager') ? 'selected' : '';
                    const selectedAdmin = (u.role === 'admin') ? 'selected' : '';

                    const roleForm = "<form novalidate action='../../Controllers/AdminUserActionController.php' method='POST' style='display:inline;'>" +
                        "<input type='hidden' name='action' value='change_role'>" +
                        "<input type='hidden' name='id' value='" + u.id + "'>" +
                        "<select name='role' onchange='this.form.submit()'>" +
                            "<option value='member' " + selectedMember + ">Member</option>" +
                            "<option value='librarian' " + selectedLibrarian + ">Librarian</option>" +
                            "<option value='branch_manager' " + selectedManager + ">Branch Manager</option>" +
                            "<option value='admin' " + selectedAdmin + ">Admin</option>" +
                        "</select>" +
                    "</form>";

                    const statusColor = u.is_active ? 'green' : 'red';
                    const statusText = u.is_active ? 'Active' : 'Inactive';
                    const statusHtml = "<b style='color: " + statusColor + ";'>" + statusText + "</b>";

                    const toggleBtnText = u.is_active ? 'Deactivate' : 'Activate';
                    const actionsHtml = "<form method='POST' action='../../Controllers/AdminUserFormController.php' style='display:inline;'>" +
                        "<input type='hidden' name='id' value='" + u.id + "'>" +
                        "<button type='submit' class='btn-link'>Edit Info</button>" +
                    "</form> " +
                    "<form method='POST' action='../../Controllers/AdminUserActionController.php' style='display:inline;'>" +
                        "<input type='hidden' name='action' value='toggle_status'>" +
                        "<input type='hidden' name='id' value='" + u.id + "'>" +
                        "<button type='submit' onclick=\"return confirm('Toggle status for this user?')\" class='btn-link'>" + toggleBtnText + "</button>" +
                    "</form>";

                    display += "<tr>";
                    display += "<td>" + u.name + "</td>";
                    display += "<td>" + u.email + "</td>";
                    display += "<td>" + roleForm + "</td>";
                    display += "<td>" + u.branch_name + "</td>";
                    display += "<td>" + statusHtml + "</td>";
                    display += "<td>" + u.created_at + "</td>";
                    display += "<td>" + actionsHtml + "</td>";
                    display += "</tr>";
                }
            }
            tbody.innerHTML = display;
        }
    };
    xhttp.open("POST", "../../Controllers/AdminUserSearchApiController.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("search=" + encodeURIComponent(searchVal) + "&role_filter=" + encodeURIComponent(roleVal));
}
