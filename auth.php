<?php
// Ensure session starts at the very top
session_start();
require 'config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Helper function to redirect with alert
function redirectWithAlert($message, $url) {
    echo "<script>alert('$message'); window.location.href='$url';</script>";
    exit();
}

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = trim(htmlspecialchars($_POST['name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim($_POST['password']);
    $mobile = trim(htmlspecialchars($_POST['mobile']));

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($mobile)) {
        redirectWithAlert('All fields are required!', 'index.html');
    }

    if (!preg_match("/^[a-zA-Z_ ]*$/", $name)) {
        redirectWithAlert('Invalid name format!', 'index.html');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectWithAlert('Invalid email format!', 'index.html');
    }

    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/", $password)) {
        redirectWithAlert('Password must be at least 6 characters long with letters and numbers!', 'index.html');
    }

    if (!preg_match("/^[0-9]{10}$/", $mobile)) {
        redirectWithAlert('Invalid mobile number! Must be 10 digits.', 'index.html');
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        redirectWithAlert('Email already registered!', 'index.html');
    }

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, mobile) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $mobile);

    if ($stmt->execute()) {
        redirectWithAlert('Registration successful!', 'index.html');
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim(htmlspecialchars($_POST['username']));
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        redirectWithAlert('Username and password are required!', 'index.html');
    }

    // Retrieve user
    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Store session data
            $_SESSION['username'] = $user['name'];
            $_SESSION['user_id'] = $user['id'];
            session_write_close();  // Ensure the session is written


            redirectWithAlert('Login successful!', '/projectkk/index.html');
        } else {
            redirectWithAlert('Invalid password!', 'index.html');
        }
    } else {
        redirectWithAlert('User not found!', 'index.html');
    }

    $stmt->close();
}

$conn->close();
?>