function validateLogin(form) {
    let email = form.email.value.trim();
    let password = form.password.value.trim();
    let isValid = true;

    document.getElementById('emailErr').innerText = "";
    document.getElementById('passwordErr').innerText = "";

    if (email === "") {
        document.getElementById('emailErr').innerText = "Email is required";
        isValid = false;
    } else if (!email.includes('@')) {
        document.getElementById('emailErr').innerText = "Invalid email format";
        isValid = false;
    }

    if (password === "") {
        document.getElementById('passwordErr').innerText = "Password is required";
        isValid = false;
    }

    return isValid;
}

function validateRegister(form) {
    let name = form.name.value.trim();
    let email = form.email.value.trim();
    let phone = form.phone.value.trim();
    let password = form.password.value.trim();
    let confirmPassword = form.confirmPassword.value.trim();
    let isValid = true;

    document.getElementById('nameErr').innerText = "";
    document.getElementById('emailErr').innerText = "";
    document.getElementById('phoneErr').innerText = "";
    document.getElementById('passwordErr').innerText = "";
    document.getElementById('confirmPasswordErr').innerText = "";

    if (name === "") {
        document.getElementById('nameErr').innerText = "Name is required";
        isValid = false;
    }

    if (email === "") {
        document.getElementById('emailErr').innerText = "Email is required";
        isValid = false;
    } else if (!email.includes('@')) {
        document.getElementById('emailErr').innerText = "Invalid email format";
        isValid = false;
    }

    if (phone === "") {
        document.getElementById('phoneErr').innerText = "Phone number is required";
        isValid = false;
    } else if (!/^[0-9+]{10,15}$/.test(phone)) {
        document.getElementById('phoneErr').innerText = "Phone number must be numeric (10-15 digits)";
        isValid = false;
    }

    if (password === "") {
        document.getElementById('passwordErr').innerText = "Password is required";
        isValid = false;
    } else if (password.length < 6) {
        document.getElementById('passwordErr').innerText = "Password must be at least 6 characters";
        isValid = false;
    }

    if (confirmPassword === "") {
        document.getElementById('confirmPasswordErr').innerText = "Please confirm your password";
        isValid = false;
    } else if (password !== confirmPassword) {
        document.getElementById('confirmPasswordErr').innerText = "Passwords do not match";
        isValid = false;
    }

    return isValid;
}
