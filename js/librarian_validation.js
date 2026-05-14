function validateGenreForm(form) {
    let name = form.name.value.trim();
    if (name === "") {
        alert("Genre name is required");
        return false;
    }
    return true;
}

function validateFineForm(form) {
    let borrowId = form.borrow_record_id.value.trim();
    let memberId = form.member_id.value.trim();
    let amount = form.amount.value.trim();
    let reason = form.reason.value.trim();
    let isValid = true;

    if (borrowId === "" && memberId === "") {
        alert("Either Borrow Record ID or Member ID is required");
        isValid = false;
    } else if (amount === "" || isNaN(amount)) {
        alert("Valid amount is required");
        isValid = false;
    } else if (reason === "") {
        alert("Reason is required");
        isValid = false;
    }

    return isValid;
}

function validateAnnouncementForm(form) {
    let title = form.title.value.trim();
    let body = form.body.value.trim();
    let isValid = true;

    if (title === "") {
        alert("Title is required");
        isValid = false;
    } else if (body === "") {
        alert("Body is required");
        isValid = false;
    }

    return isValid;
}
