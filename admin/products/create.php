<?php
/**
 * @file create.php
 * @version 1.0
 * @author JHL
 * @brief Script para la creación de un nuevo producto en la base de datos.
 *
 * Este script maneja la lógica para añadir un nuevo producto, incluyendo la subida
 * de una imagen y la inserción de los datos en las tablas `productos` y `producto_variantes`.
 */

// Incluye el archivo de configuración para la conexión a la base de datos.
require_once '../../config/database.php';

// Verifica si la solicitud se realizó mediante el método POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Crea una nueva conexión a la base de datos.
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    // Comprueba si hay errores en la conexión.
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Recoge los datos del formulario.
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $tallas = $_POST['tallas'];

    $imagen = ''; // Inicializa la ruta de la imagen.

    // Procesa la subida de la imagen si se ha enviado un archivo.
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../Images/'; // Directorio donde se guardarán las imágenes.
        $file_name = basename($_FILES['foto']['name']);
        $target_file = $upload_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verifica si el archivo es una imagen real.
        $check = getimagesize($_FILES['foto']['tmp_name']);
        if ($check !== false) {
            // Valida los formatos de archivo permitidos.
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" && $imageFileType != "webp") {
                echo "Error: Solo se permiten archivos JPG, JPEG, PNG, GIF y WEBP.";
                exit;
            }

            // Si el archivo ya existe, le añade un sufijo numérico para evitar sobreescribirlo.
            $i = 0;
            while (file_exists($target_file)) {
                $i++;
                $file_name = pathinfo(basename($_FILES['foto']['name']), PATHINFO_FILENAME) . "_" . $i . "." . $imageFileType;
                $target_file = $upload_dir . $file_name;
            }

            // Mueve el archivo subido al directorio de destino.
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $imagen = 'Images/' . $file_name; // Ruta relativa para guardar en la base de datos.
            } else {
                echo "Error: Hubo un problema al subir el archivo.";
                exit;
            }
        } else {
            echo "Error: El archivo no es una imagen válida.";
            exit;
        }
    }

    // Prepara la consulta SQL para insertar el producto en la tabla `productos`.
    $sql = "INSERT INTO productos (nombre, descripcion, precio, foto) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // Vincula los parámetros para evitar inyección SQL.
    $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $file_name);
    $stmt->execute();

    // Obtiene el ID del producto recién insertado.
    $product_id = $conn->insert_id;

    // Inserta las variantes de talla si se han proporcionado.
    if (!empty($tallas)) {
        $tallas = strtolower(trim($tallas));
        $stmt_variante = $conn->prepare("INSERT INTO producto_variantes (producto_id, talla) VALUES (?, ?)");
        $stmt_variante->bind_param("is", $product_id, $tallas);
        $stmt_variante->execute();
        $stmt_variante->close();
    }

    // Cierra la conexión y el statement.
    $stmt->close();
    $conn->close();

    // Redirige al usuario a la página principal de productos.
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
        <h1>Añadir Nuevo Producto</h1>
        <!-- 
            Formulario para crear un producto.
            - `action="create.php"`: El formulario se envía a este mismo archivo.
            - `method="post"`: Los datos se envían de forma segura.
            - `enctype="multipart/form-data"`: Necesario para poder subir archivos.
        -->
        <form action="create.php" method="post" enctype="multipart/form-data">
            <!-- Campo para el nombre del producto -->
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" required>

            <!-- Campo para la descripción del producto -->
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" required></textarea>

            <!-- Campo para el precio del producto -->
            <label for="precio">Precio</label>
            <input type="number" name="precio" id="precio" step="0.01" required>

            <!-- Campo para seleccionar la talla del producto -->
            <label for="tallas">Tallas</label>
            <select name="tallas" id="tallas">
                <option value="">Seleccionar</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
            </select>

            <!-- Campo para subir la imagen del producto -->
            <label for="foto">Imagen (Archivo)</label>
            <input type="file" name="foto" id="foto" accept="image/*" required>

            <!-- Botones de acción -->
            <button type="submit" class="btn btn-add">Añadir</button>
            <button type="button" class="btn btn-cancel" onclick="history.back()">Cancelar</button>
        </form>
    </div>
</body>
</html>