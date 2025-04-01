<?php
// Allow CORS for cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Ensure JSON response and enable error reporting
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

session_start();

// Debug: Check session status
if (!isset($_SESSION['username'])) {
    echo json_encode([
        "success" => false,
        "message" => "❌ User not logged in.",
        "session_status" => session_status(),
        "session_data" => $_SESSION
    ]);
    exit;
}

$username = $_SESSION['username'];

// Debug: Validate database connection
if (!$conn) {
    echo json_encode(["success" => false, "message" => "❌ Database connection failed."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    // Debug: Check for upload errors
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            "success" => false,
            "message" => "❌ Upload error: " . $_FILES['file']['error']
        ]);
        exit;
    }

    // Fetch user_id based on username
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "❌ Database error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id) {
        echo json_encode(["success" => false, "message" => "❌ User not found."]);
        exit;
    }

    $fileName = basename($_FILES['file']['name']);
    $uploadDir = 'uploads/';
    $filePath = $uploadDir . time() . '_' . $fileName;

    // Ensure the upload directory exists and is writable
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        echo json_encode(["success" => false, "message" => "❌ Failed to create upload directory."]);
        exit;
    }

    if (!is_writable($uploadDir)) {
        echo json_encode(["success" => false, "message" => "❌ Upload directory is not writable."]);
        exit;
    }

    // Move the uploaded file
    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        $stmt = $conn->prepare("INSERT INTO uploads (file_name, file_path, user_id) VALUES (?, ?, ?)");
        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "❌ Database error: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("ssi", $fileName, $filePath, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "✅ File uploaded successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "❌ Database error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "❌ Failed to move uploaded file."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "❌ Invalid request."]);
}

$conn->close();
?>