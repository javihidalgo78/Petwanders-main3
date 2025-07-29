<?php
require_once 'config/database.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$availability = isset($_GET['availability']) ? $_GET['availability'] : null;
$amazonOnly = isset($_GET['amazonOnly']) ? $_GET['amazonOnly'] : null;
$category = isset($_GET['category']) ? $_GET['category'] : null;
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : null;

$sql = "SELECT * FROM productos WHERE 1=1";

if ($availability !== null) {
    $isAvailable = ($availability === 'true');
    $sql .= " AND disponible = " . ($isAvailable ? 1 : 0);
}

if ($amazonOnly === 'true') {
    $sql .= " AND amazon_url IS NOT NULL AND amazon_url != ''";
}

if ($category) {
    $sql .= " AND categoria = '" . $conn->real_escape_string($category) . "'";
}

if ($sortBy) {
    if ($sortBy == 'price_asc') {
        $sql .= " ORDER BY precio ASC";
    } elseif ($sortBy == 'price_desc') {
        $sql .= " ORDER BY precio DESC";
    }
}

$result = $conn->query($sql);

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

header('Content-Type: application/json');
echo json_encode($products);

$conn->close();
?>