<?php
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['file']['name']);
    $uploaderName = $_POST['uploaderName'];
    $date = $_POST['date'];

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        $sql = "INSERT INTO uploads (filename, uploader_name, upload_date) VALUES ('$uploadFile', '$uploaderName', '$date')";
        if ($conn->query($sql) === TRUE) {
            echo 'File is valid, and was successfully uploaded.';
        } else {
            echo 'Error: ' . $sql . '<br>' . $conn->error;
        }
    } else {
        echo 'Possible file upload attack!';
    }
} else {
    echo 'No file uploaded.';
}

$conn->close();
?>
