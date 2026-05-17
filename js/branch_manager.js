function validateManagerProfile(form) {
    let name = form.name.value.trim();
    let email = form.email.value.trim();
    let phone = form.phone.value.trim();
    let newPassword = form.new_password.value;
    let confirmPassword = form.confirm_password.value;

    if (name === "") {
        alert("Name is required");
        return false;
    }
    if (email === "" || !email.includes("@")) {
        alert("Valid email is required");
        return false;
    }
    if (phone === "") {
        alert("Phone is required");
        return false;
    }
    if (newPassword !== "" && newPassword !== confirmPassword) {
        alert("Passwords do not match");
        return false;
    }

    return true;
}

function validateBranchForm(form) {
    if (form.name.value.trim() === "") {
        alert("Branch name is required");
        return false;
    }
    if (form.address.value.trim() === "") {
        alert("Address is required");
        return false;
    }
    if (form.city.value.trim() === "") {
        alert("City is required");
        return false;
    }
    if (form.phone.value.trim() === "") {
        alert("Phone is required");
        return false;
    }

    return true;
}

function validateStaffAssignForm(form) {
    if (form.librarian_id.value === "") {
        alert("Please select a librarian");
        return false;
    }
    if (form.branch_id.value === "") {
        alert("Please select a branch");
        return false;
    }

    return true;
}

function validatePolicyForm(form) {
    let maxBorrowDays = parseInt(form.max_borrow_days.value, 10);
    let maxBooks = parseInt(form.max_books_per_member.value, 10);
    let fineRate = parseFloat(form.fine_rate_per_day.value);
    let maxRenewals = parseInt(form.max_renewals.value, 10);

    if (isNaN(maxBorrowDays) || maxBorrowDays < 1) {
        alert("Max borrow days must be at least 1");
        return false;
    }
    if (isNaN(maxBooks) || maxBooks < 1) {
        alert("Max books per member must be at least 1");
        return false;
    }
    if (isNaN(fineRate) || fineRate < 0) {
        alert("Fine rate must be zero or greater");
        return false;
    }
    if (isNaN(maxRenewals) || maxRenewals < 0) {
        alert("Max renewals must be zero or greater");
        return false;
    }

    return true;
}

function validateManagerAnnouncementForm(form) {
    if (form.title.value.trim() === "") {
        alert("Title is required");
        return false;
    }
    if (form.body.value.trim() === "") {
        alert("Content is required");
        return false;
    }

    return true;
}

function escapeHtml(value) {
    return String(value === null || value === undefined ? "" : value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function loadOverdueAlerts() {
    let thresholdInput = document.getElementById("threshold_days");
    let message = document.getElementById("overdue_alert_message");
    let rows = document.getElementById("overdue_alert_rows");

    if (!thresholdInput || !message || !rows) {
        return;
    }

    let threshold = thresholdInput.value.trim();
    message.innerText = "Loading overdue alerts...";

    let xhr = new XMLHttpRequest();
    xhr.open("GET", "../../Controllers/BranchManagerOverdueAlertsApiController.php?threshold_days=" + encodeURIComponent(threshold), true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState !== 4) {
            return;
        }

        if (xhr.status !== 200) {
            message.innerText = "Unable to load overdue alerts";
            rows.innerHTML = "<tr><td colspan=\"7\">No alert data loaded.</td></tr>";
            return;
        }

        let response;
        try {
            response = JSON.parse(xhr.responseText);
        } catch (e) {
            message.innerText = "Invalid alert response";
            rows.innerHTML = "<tr><td colspan=\"7\">No alert data loaded.</td></tr>";
            return;
        }

        if (!response.success) {
            message.innerText = response.message || "Unable to load overdue alerts";
            rows.innerHTML = "<tr><td colspan=\"7\">No alert data loaded.</td></tr>";
            return;
        }

        message.innerText = "Showing loans overdue by more than " + response.threshold_days + " day(s).";

        if (!response.alerts || response.alerts.length === 0) {
            rows.innerHTML = "<tr><td colspan=\"7\">No overdue alerts found for this threshold.</td></tr>";
            return;
        }

        let html = "";
        for (let i = 0; i < response.alerts.length; i++) {
            let alertRow = response.alerts[i];
            html += "<tr>";
            html += "<td>" + escapeHtml(alertRow.borrow_record_id) + "</td>";
            html += "<td>" + escapeHtml(alertRow.branch_name) + "</td>";
            html += "<td>" + escapeHtml(alertRow.member_name) + "</td>";
            html += "<td>" + escapeHtml(alertRow.member_email) + "</td>";
            html += "<td>" + escapeHtml(alertRow.book_title) + "</td>";
            html += "<td>" + escapeHtml(alertRow.due_date) + "</td>";
            html += "<td>" + escapeHtml(alertRow.overdue_days) + "</td>";
            html += "</tr>";
        }

        rows.innerHTML = html;
    };

    xhr.send();
}
