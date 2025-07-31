<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $tallas = $_POST['tallas'];

    $imagen = ''; // Initialize image path
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../Images/'; // Directory to save uploaded images
        $file_name = basename($_FILES['foto']['name']);
        $target_file = $upload_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['foto']['tmp_name']);
        if ($check !== false) {
            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" && $imageFileType != "webp") {
                echo "Sorry, only JPG, JPEG, PNG, GIF & WEBP files are allowed.";
                exit;
            }

            // Check if file already exists, if so, rename it
            $i = 0;
            while (file_exists($target_file)) {
                $i++;
                $file_name = pathinfo(basename($_FILES['foto']['name']), PATHINFO_FILENAME) . "_" . $i . "." . $imageFileType;
                $target_file = $upload_dir . $file_name;
            }

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $imagen = 'Images/' . $file_name; // Path to save in DB
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        } else {
            echo "File is not an image.";
            exit;
        }
    }

    $sql = "INSERT INTO productos (nombre, descripcion, precio, foto) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $file_name);
    $stmt->execute();

    $product_id = $conn->insert_id;

    // Insertar tallas
    if (!empty($tallas)) {
        $tallas = strtolower(trim($tallas));
        $stmt_variante = $conn->prepare("INSERT INTO producto_variantes (producto_id, talla) VALUES (?, ?)");
        $stmt_variante->bind_param("is", $product_id, $tallas);
        $stmt_variante->execute();
        $stmt_variante->close();
    }

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Producto</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="container">
        <h1>Añadir Producto</h1>
        <form action="create.php" method="post" enctype="multipart/form-data">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" required></textarea>

            <label for="precio">Precio</label>
            <input type="number" name="precio" id="precio" step="0.01" required>

            <label for="tallas">Tallas</label>
            <select name="tallas" id="tallas">
                <option value="">Seleccionar</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
            </select>

            

            <label for="foto">Imagen (Archivo)</label>
            <input type="file" name="foto" id="foto" accept="image/*" required>

            <button type="submit" class="btn btn-add">Añadir</button>
            <button type="button" class="btn btn-cancel" onclick="history.back()">Cancelar</button>
        </form>
    </div>
</body>
</html>
