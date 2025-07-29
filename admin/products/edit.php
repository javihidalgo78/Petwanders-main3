<?php
// Assuming you have a database connection established in another file
// and you get the product ID from the URL, e.g., edit.php?id=1
include_once '../../config/config.php'; // Your database connection file

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

if (!isset($_GET['id'])) {
    die("Product ID not specified.");
}

$product_id = intval($_GET['id']);

// Fetch product details
$product_result = $conn->query("SELECT * FROM productos WHERE id = $product_id");
if ($product_result->num_rows > 0) {
    $product = $product_result->fetch_assoc();
} else {
    die("Product not found.");
}

// Fetch product variations
$variations_result = $conn->query("SELECT * FROM product_variations WHERE product_id = $product_id");
$variations = [];
while ($row = $variations_result->fetch_assoc()) {
    $variations[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="container">
        <h1>Editar Producto</h1>
        <form action="edit.php" method="post">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo $product['nombre']; ?>" required>

            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" required><?php echo $product['descripcion']; ?></textarea>

            <label for="precio">Precio</label>
            <input type="number" name="precio" id="precio" step="0.01" value="<?php echo $product['precio']; ?>" required>

            <div class="filtro-item">
                <label for="filtro-favoritos">Tallas</label>
                <select id="filtro-favoritos">
                    <option value="">S</option>
                    <option value="1">M</option>
                    <option value="0">L</option>
                </select>
            </div>

            <div class="form-group">
                <label for="filtro-favoritos">Stock</label>
                <select id="filtro-favoritos">

                </select>
            </div>

            <div class="form-group">
            <label for="imagen">Imagen</label>
            <input type="file" id="imagen" name="imagen" accept="image/*">
            <small class="error" id="error-imagen"></small>
        </div>

            <button type="submit" class="btn">Actualizar</button>
            <a href="index.php" class="btn">Cancelar</a>
        </form>
    </div>
</body>
</html>

