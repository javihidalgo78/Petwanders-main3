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
    $capacidades = $_POST['capacidades'];
    $colores = $_POST['colores'];
    $imagen = $_POST['imagen'];

    $sql = "INSERT INTO productos (nombre, descripcion, precio, tallas, capacidades, colores, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssss", $nombre, $descripcion, $precio, $tallas, $capacidades, $colores, $imagen);
    $stmt->execute();

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
        <form action="create.php" method="post">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" required></textarea>

            <label for="precio">Precio</label>
            <input type="number" name="precio" id="precio" step="0.01" required>

            <label for="tallas">Tallas (separadas por comas)</label>
            <input type="text" name="tallas" id="tallas">

            <label for="capacidades">Capacidades (separadas por comas)</label>
            <input type="text" name="capacidades" id="capacidades">

            <label for="colores">Colores (separadas por comas)</label>
            <input type="text" name="colores" id="colores">

            <label for="imagen">Imagen (URL)</label>
            <input type="text" name="imagen" id="imagen" required>

            <button type="submit" class="btn">Añadir</button>
        </form>
    </div>
</body>
</html>
