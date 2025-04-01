// Ensure the login form is shown on page load
document.addEventListener("DOMContentLoaded", () => {
    showLogin();
});

// Show Login Form
function showLogin() {
    document.getElementById("login-form").style.display = "block";
    document.getElementById("register-form").style.display = "none";
}

// Show Register Form
function showRegister() {
    document.getElementById("login-form").style.display = "none";
    document.getElementById("register-form").style.display = "block";
}

// Validate Register Form (Ensure password and mobile validations)
function validateRegister() {
    const password = document.getElementById("register-password").value;
    const confirmPassword = document.getElementById("register-confirm-password").value;
    const mobileNumber = document.getElementById("register-mobile").value;

    // Password Regex: At least 6 characters, one uppercase, one lowercase, one number
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/;

    // Check Password Strength
    if (!passwordRegex.test(password)) {
        alert("Password must be at least 6 characters, include one uppercase letter, one lowercase letter, and one number.");
        return false; // Stop form submission
    }

    // Check if Passwords Match
    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return false;
    }

    // Validate Mobile Number (Only digits and exactly 10 characters)
    const mobileRegex = /^[0-9]{10}$/;
    if (!mobileRegex.test(mobileNumber)) {
        alert("Please enter a valid 10-digit mobile number.");
        return false;
    }

    alert("Validation successful!");
    return true; // Allow form submission
}

// Forgot Password Feature (Mock-up)
function showForgotPassword() {
    const email = prompt("Enter your registered email to reset your password:");
    if (email) {
        alert("A password reset link has been sent to: " + email);
    } else {
        alert("Please enter a valid email address.");
    }
}

// Restrict Mobile Number Input to Digits Only
document.addEventListener("input", function (event) {
    if (event.target.id === "register-mobile") {
        event.target.value = event.target.value.replace(/[^0-9]/g, "");
    }
});
