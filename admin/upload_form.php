<?php
session_start();
// Simple check to ensure the user is an admin.
// In a real-world application, you'd have a more robust role-based access control system.
if (!isset($_SESSION['admin'])) {
    // Redirect to login page or show an error
    header('Location: index.php'); // Assuming your admin login is at admin/index.php
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Nuevo Producto</title>
    <link rel="stylesheet" href="css/estilos.css"> 
</head>
<body>
    <div class="container">
        <h2>Subir Nuevo Producto</h2>
        <form action="process_upload.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" step="0.01" id="precio" name="precio" required>
            </div>
            <div class="form-group">
                <label for="categoria">Categoría:</label>
                <input type="text" id="categoria" name="categoria" required>
            </div>
            <div class="form-group">
                <label for="foto">Imagen del Producto:</label>
                <input type="file" id="foto" name="foto" accept="image/*" required>
            </div>
            <button type="submit" name="submit">Subir Producto</button>
        </form>
    </div>
</body>
</html>