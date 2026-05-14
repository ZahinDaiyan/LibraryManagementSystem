function validateComplaintForm(form) {
    let title = form.title.value.trim();
    let description = form.description.value.trim();
    let isValid = true;

    if (title === "") {
        alert("Complaint title is required");
        isValid = false;
    } else if (description === "") {
        alert("Please provide a detailed description of your complaint");
        isValid = false;
    }

    return isValid;
}

function validateProfileUpdate(form) {
    let name = form.name.value.trim();
    let email = form.email.value.trim();
    let phone = form.phone.value.trim();
    let isValid = true;

    if (name === "") {
        alert("Name is required");
        isValid = false;
    } else if (email === "") {
        alert("Email is required");
        isValid = false;
    } else if (phone === "") {
        alert("Phone number is required");
        isValid = false;
    }

    return isValid;
}

function validatePasswordChange(form) {
    let current = form.current_password.value.trim();
    let newPass = form.new_password.value.trim();
    let confirm = form.confirm_password.value.trim();
    let isValid = true;

    if (current === "") {
        alert("Current password is required");
        isValid = false;
    } else if (newPass === "") {
        alert("New password is required");
        isValid = false;
    } else if (newPass.length < 6) {
        alert("New password must be at least 6 characters");
        isValid = false;
    } else if (newPass !== confirm) {
        alert("Passwords do not match");
        isValid = false;
    }

    return isValid;
}

function validateReviewForm(form) {
    let comment = form.comment.value.trim();
    if (comment === "") {
        alert("Please provide a comment for your review");
        return false;
    }
    return true;
}
