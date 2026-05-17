function validateGenreForm(form) {
	let name = form.name.value.trim();
	let isValid = true;

	if (name === "") {
		alert("Genre name is required");
		isValid = false;
	} else if (name.length > 80) {
		alert("Genre name must be 80 characters or less");
		isValid = false;
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

function validateFineForm(form) {
	let borrowId = form.borrow_record_id ? String(form.borrow_record_id.value).trim() : "";
	let memberId = form.member_id ? String(form.member_id.value).trim() : "";
	let amount = form.amount ? String(form.amount.value).trim() : "";
	let reason = form.reason ? String(form.reason.value).trim() : "";
	let isValid = true;

	if (borrowId === "" && memberId === "") {
		alert("Borrow record ID or Member ID is required");
		isValid = false;
	} else if (amount === "") {
		alert("Amount is required");
		isValid = false;
	} else if (isNaN(amount) || Number(amount) <= 0) {
		alert("Amount must be a positive number");
		isValid = false;
	} else if (reason === "") {
		alert("Reason is required");
		isValid = false;
	}

	return isValid;
}

function validateAnnouncementForm(form) {
	let title = form.title ? String(form.title.value).trim() : "";
	let body = form.body ? String(form.body.value).trim() : "";
	let branch = form.branch_id ? String(form.branch_id.value).trim() : "";
	let isValid = true;

	if (title === "") {
		alert("Title is required");
		isValid = false;
	} else if (body === "") {
		alert("Body is required");
		isValid = false;
	} else if (branch !== "" && !/^[0-9]+$/.test(branch)) {
		alert("Branch ID must be an integer");
		isValid = false;
	}

	return isValid;
}

function validateProfileUpdate(form) {
	let name = form.name.value.trim();
	let email = form.email.value.trim();
	let phone = form.phone.value.trim();
	let currentPassword = form.current_password ? form.current_password.value.trim() : "";
	let newPassword = form.new_password ? form.new_password.value.trim() : "";
	let confirmPassword = form.confirm_password ? form.confirm_password.value.trim() : "";
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

	if (isValid && (newPassword !== "" || confirmPassword !== "")) {
		if (currentPassword === "") {
			alert("Current password is required to change your password");
			isValid = false;
		} else if (newPassword === "") {
			alert("New password is required");
			isValid = false;
		} else if (newPassword.length < 6) {
			alert("New password must be at least 6 characters");
			isValid = false;
		} else if (newPassword !== confirmPassword) {
			alert("Passwords do not match");
			isValid = false;
		}
	}

	return isValid;
}

// Librarian-only profile validator (no current password required client-side)
function validateProfileUpdate(form) {
	let name = form.name.value.trim();
	let email = form.email.value.trim();
	let phone = form.phone.value.trim();
	let newPassword = form.new_password ? form.new_password.value.trim() : "";
	let confirmPassword = form.confirm_password ? form.confirm_password.value.trim() : "";
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

	if (isValid && (newPassword !== "" || confirmPassword !== "")) {
		if (newPassword === "") {
			alert("New password is required");
			isValid = false;
		} else if (newPassword.length < 6) {
			alert("New password must be at least 6 characters");
			isValid = false;
		} else if (newPassword !== confirmPassword) {
			alert("Passwords do not match");
			isValid = false;
		}
	}

	return isValid;
}

