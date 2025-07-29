<?php
require_once 'config/config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT DISTINCT categoria FROM productos");

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row['categoria'];
}

header('Content-Type: application/json');
echo json_encode($categories);

$conn->close();
?>
