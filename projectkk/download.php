<?php
// Include database connection
include 'db.php';

// Helper function to log debugging info
function debugLog($message) {
    error_log($message);
    echo "<pre>$message</pre>";
}

// Ensure ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Error: No file ID provided.");
}

$id = intval($_GET['id']);
debugLog("🔍 Debug: File ID = $id");

// Fetch file details from database
$stmt = $conn->prepare("SELECT file_name, file_path FROM uploads WHERE id = ?");
if (!$stmt) {
    debugLog("❌ Error: Prepare statement failed - " . $conn->error);
    die("Database error: Prepare failed.");
}

$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

// If file exists in DB
if ($stmt->num_rows > 0) {
    $stmt->bind_result($fileName, $filePath);
    $stmt->fetch();

    debugLog("📂 Debug: File Path = $filePath");
    debugLog("📄 Debug: File Name = $fileName");

    // Ensure the file exists in the filesystem
    if (file_exists($filePath)) {
        // Set headers for downloading the file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
        header('Content-Length: ' . filesize($filePath));

        // Clean output buffer and send the file
        ob_clean();
        flush();
        readfile($filePath);
        exit;
    } else {
        debugLog("❌ Error: File does not exist on the server.");
        die("Error: File not found.");
    }
} else {
    debugLog("❌ Error: No record for ID $id.");
    die("Error: Invalid file ID.");
}

$stmt->close();
$conn->close();
?>
