<?php
include 'db.php';

$result = $conn->query("SELECT id, file_name FROM uploads ORDER BY id DESC");
$files = [];

while ($row = $result->fetch_assoc()) {
    $files[] = $row;
}

echo json_encode($files);
$conn->close();
?>
