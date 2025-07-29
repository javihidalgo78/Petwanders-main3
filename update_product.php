<?php
require_once '../../config/database.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get the main product details
    $product_id = intval($_POST['product_id']);
$name = $conn->real_escape_string($_POST['name']);
$description = $conn->real_escape_string($_POST['description']);
$price = floatval($_POST['price']);

// Update the main product details in the 'products' table
$update_product_sql = "UPDATE products SET name = '$name', description = '$description', price = $price WHERE id = $product_id";

if ($conn->query($update_product_sql) === TRUE) {
    echo "Product details updated successfully.<br>";
} else {
    echo "Error updating product: " . $conn->error . "<br>";
}

// Check if stock data is submitted
if (isset($_POST['stock']) && is_array($_POST['stock'])) {
    // Loop through the submitted stock data and update each variation
    foreach ($_POST['stock'] as $variation_id => $stock) {
        $variation_id = intval($variation_id);
        $stock = intval($stock);

        $update_variation_sql = "UPDATE product_variations SET stock = $stock WHERE id = $variation_id AND product_id = $product_id";

        if ($conn->query($update_variation_sql) !== TRUE) {
            echo "Error updating stock for variation ID $variation_id: " . $conn->error . "<br>";
        }
    }
    echo "Stock levels updated successfully.";
}

// Redirect back to the product list or edit page
header("Location: edit.php?id=$product_id&status=success");
exit();

} else {
    // If not a POST request, redirect to the main page or show an error
    header("Location: index.php");
    exit();
}

$conn->close();
?>