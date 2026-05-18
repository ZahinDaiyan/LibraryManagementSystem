function validateUserForm(form) {
    let name = form.name.value.trim();
    let email = form.email.value.trim();
    let phone = form.phone.value.trim();
    let isValid = true;

    // Clearing previous errors if they were shown via JS
    // (In this project, errors are mostly server-side, but we add JS for immediate feedback)
    
    if (name === "") {
        alert("Name is required");
        isValid = false;
    } else if (email === "") {
        alert("Email is required");
        isValid = false;
    } else if (!email.includes('@')) {
        alert("Invalid email format");
        isValid = false;
    } else if (phone === "") {
        alert("Phone number is required");
        isValid = false;
    } else if (!/^[0-9+]{10,15}$/.test(phone)) {
        alert("Phone number must be numeric (10-15 digits)");
        isValid = false;
    }

    // Password is only required for new accounts (action === 'create')
    if (form.action.value === 'create') {
        let password = form.password.value.trim();
        if (password === "") {
            alert("Password is required for new accounts");
            isValid = false;
        }
    }

    return isValid;
}

function validateBookForm(form) {
    let title = form.title.value.trim();
    let author = form.author.value.trim();
    let isbn = form.isbn.value.trim();
    let genreId = form.genre_id.value.trim();
    let publisher = form.publisher.value.trim();
    let publishedYear = form.published_year.value.trim();
    let quantity = form.quantity ? String(form.quantity.value).trim() : "";
    let isValid = true;

    if (title === "") {
        alert("Book title is required");
        isValid = false;
    } else if (author === "") {
        alert("Author name is required");
        isValid = false;
    } else if (isbn === "") {
        alert("ISBN is required");
        isValid = false;
    } else if (!/^[0-9]+$/.test(isbn)) {
        alert("ISBN must contain only numbers");
        isValid = false;
    } else if (genreId === "") {
        alert("Genre is required");
        isValid = false;
    } else if (publisher === "") {
        alert("Publisher is required");
        isValid = false;
    } else if (publishedYear === "") {
        alert("Published year is required");
        isValid = false;
    } else if (isNaN(publishedYear) || publishedYear < 1800 || publishedYear > new Date().getFullYear()) {
        alert("Published year must be a valid year");
        isValid = false;
    }

    if (isValid && form.quantity) {
        if (quantity === "") {
            alert("Quantity is required");
            isValid = false;
        } else if (isNaN(quantity) || Number(quantity) < 1) {
            alert("Quantity must be a valid number greater than 0");
            isValid = false;
        }
    }

    // Description is optional on purpose.

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
        alert("Content is required");
        isValid = false;
    }

    return isValid;
}

function validateSettingsForm(form) {
    let fineRate = form.default_fine_rate.value.trim();
    let maxDays = form.default_max_borrow_days.value.trim();
    let maxBooks = form.default_max_books_per_member.value.trim();
    let isValid = true;

    if (fineRate === "" || isNaN(fineRate)) {
        alert("Fine rate must be a valid number");
        isValid = false;
    } else if (maxDays === "" || isNaN(maxDays)) {
        alert("Max borrow days must be a number");
        isValid = false;
    } else if (maxBooks === "" || isNaN(maxBooks)) {
        alert("Max books per member must be a number");
        isValid = false;
    }

    return isValid;
}

function validateComplaintResponse(form) {
    let response = form.admin_response.value.trim();
    if (response === "") {
        alert("Please provide a response before submitting");
        return false;
    }
    return true;
}
