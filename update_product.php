<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize and retrieve form data
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre = isset($_POST['nombre']) ? $conn->real_escape_string($_POST['nombre']) : '';
    $descripcion = isset($_POST['descripcion']) ? $conn->real_escape_string($_POST['descripcion']) : '';
    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0.0;

    // Handle file upload
    $foto = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $foto_name = basename($_FILES['imagen']['name']);
        $target_dir = "Images/";
        $target_file = $target_dir . $foto_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            $foto = $foto_name;
        }
    }

    if ($id > 0) {
        // Build the update query
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?";
        $params = ['sss', $nombre, $descripcion, $precio];

        if (!empty($foto)) {
            $sql .= ", foto = ?";
            $params[0] .= 's';
            $params[] = $foto;
        }

        $sql .= " WHERE id = ?";
        $params[0] .= 'i';
        $params[] = $id;

        // Prepare and execute the statement
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param(...$params);
            if ($stmt->execute()) {
                header("Location: admin/products/index.php?status=success");
            } else {
                header("Location: admin/products/edit.php?id=" . $id . "&status=error");
            }
            $stmt->close();
        } else {
            // Handle statement preparation error
            header("Location: admin/products/edit.php?id=" . $id . "&status=error");
        }
    } else {
        // Handle invalid ID
        header("Location: admin/products/index.php?status=invalid_id");
    }

    $conn->close();
} else {
    // Redirect if not a POST request
    header("Location: admin/products/index.php");
}
exit();
?>