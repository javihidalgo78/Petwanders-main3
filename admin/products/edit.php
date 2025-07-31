<?php
// Assuming you have a database connection established in another file
// and you get the product ID from the URL, e.g., edit.php?id=1
include_once '../../config/config.php'; // Your database connection file

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    //TODO enviar al index
    die("Product ID not specified.");
}

$product_id = 0;
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
} elseif (isset($_POST['id'])) {
    $product_id = intval($_POST['id']);
}

// Fetch product details
$product_result = $conn->query("SELECT * FROM productos WHERE id = $product_id");
if ($product_result->num_rows > 0) {
    $product = $product_result->fetch_assoc();
} else {
    die("Product not found.");
}

//comprobar si la variable $_POST contiene datos y actualizar el producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $imagen = $product['foto']; // Default to existing image

    // Handle image upload - CORREGIDO: usar 'foto' en lugar de 'imagen'
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../Images/'; // Directory to save uploaded images
        
        // Crear directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
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

            // Check file size (limit to 5MB)
            if ($_FILES['foto']['size'] > 5000000) {
                echo "Sorry, your file is too large.";
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
                // Eliminar imagen anterior si existe y es diferente
                if (!empty($product['foto']) && $product['foto'] !== 'Images/' . $file_name) {
                    $old_image_path = '../../' . $product['foto'];
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
                $imagen = 'Images/' . $file_name; // Path to save in DB
                echo "<p style='color: green;'>Imagen subida correctamente: " . $file_name . "</p>";
            } else {
                echo "<p style='color: red;'>Sorry, there was an error uploading your file.</p>";
                exit;
            }
        } else {
            echo "<p style='color: red;'>File is not an image.</p>";
            exit;
        }
    }

    $update_sql = "UPDATE productos SET nombre='$nombre', descripcion='$descripcion', precio=$precio, foto='$imagen' WHERE id=$product_id";
    if ($conn->query($update_sql)) {
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; margin: 10px 0; border-radius: 4px;'>";
        echo "<p><strong>Producto actualizado correctamente:</strong></p>";
        echo "<p>Nombre: " . htmlspecialchars($nombre) . "</p>";
        echo "<p>Descripción: " . htmlspecialchars($descripcion) . "</p>";
        echo "<p>Precio: €" . number_format($precio, 2) . "</p>";
        echo "<p>Imagen: " . htmlspecialchars($imagen) . "</p>";
        echo "</div>";
        
        // Handle variations update here
        // Delete existing variations for this product
        $conn->query("DELETE FROM producto_variantes WHERE producto_id = $product_id");

        // Insert new variations
        if (isset($_POST['tallas']) && !empty($_POST['tallas'])) {
            $talla = strtolower(trim($_POST['tallas']));
            if (!empty($talla) && in_array($talla, ['s', 'm', 'l'])) {
                $stmt_variante = $conn->prepare("INSERT INTO producto_variantes (producto_id, talla) VALUES (?, ?)");
                $stmt_variante->bind_param("is", $product_id, $talla);
                $stmt_variante->execute();
                $stmt_variante->close();
            }
        }
        
        // Actualizar los datos del producto para mostrar los nuevos valores
        $product_result = $conn->query("SELECT * FROM productos WHERE id = $product_id");
        $product = $product_result->fetch_assoc();
        
        // Optionally redirect after a delay
        echo "<script>setTimeout(function(){ window.location.href = 'index.php?updated=1'; }, 3000);</script>";
    } else {
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 4px;'>";
        echo "<p><strong>Error updating product:</strong> " . $conn->error . "</p>";
        echo "<p>SQL: " . htmlspecialchars($update_sql) . "</p>";
        echo "</div>";
    }
}

// Fetch product variations
$variations_result = $conn->query("SELECT talla FROM producto_variantes WHERE producto_id = $product_id");
$current_tallas = [];
while ($row = $variations_result->fetch_assoc()) {
    $current_tallas[] = $row['talla'];
}

$current_tallas_str = implode(', ', $current_tallas);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .current-image {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 10px 0;
        }
        .image-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .form-buttons {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Producto</h1>
        <form action="edit.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($product['nombre']); ?>" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4" required><?php echo htmlspecialchars($product['descripcion']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="precio">Precio (€)</label>
                <input type="number" name="precio" id="precio" step="0.01" value="<?php echo $product['precio']; ?>" required>
            </div>

            <div class="form-group">
                <label for="tallas">Tallas</label>
                <select name="tallas" id="tallas">
                    <option value="">Seleccionar</option>
                    <option value="s" <?php echo (in_array('s', $current_tallas)) ? 'selected' : ''; ?>>S</option>
                    <option value="m" <?php echo (in_array('m', $current_tallas)) ? 'selected' : ''; ?>>M</option>
                    <option value="l" <?php echo (in_array('l', $current_tallas)) ? 'selected' : ''; ?>>L</option>
                </select>
            </div>

            <div class="form-group">
                <label>Imagen Actual</label>
                <?php if (!empty($product['foto'])): ?>
                    <div class="image-info">
                        <p><strong>Archivo actual:</strong> <?php echo htmlspecialchars($product['foto']); ?></p>
                        <?php if (file_exists('../../Images/' . $product['foto'])): ?>
                            <img src="../../Images/<?php echo htmlspecialchars($product['foto']); ?>" alt="Imagen actual" class="current-image">
                        <?php else: ?>
                            <p style="color: red;">⚠️ Archivo no encontrado en el servidor</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #666;">No hay imagen asignada</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="foto">Nueva Imagen (opcional)</label>
                <input type="file" name="foto" id="foto" accept="image/*">
                <small style="color: #666; display: block; margin-top: 5px;">
                    Formatos permitidos: JPG, JPEG, PNG, GIF, WEBP (máximo 5MB)
                </small>
            </div>

            <div class="form-group form-buttons">
                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        // Preview de la imagen antes de subir
        document.getElementById('foto').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Crear preview
                    let preview = document.getElementById('image-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.id = 'image-preview';
                        preview.innerHTML = '<p><strong>Vista previa:</strong></p>';
                        event.target.parentNode.appendChild(preview);
                    }
                    
                    let img = preview.querySelector('img');
                    if (!img) {
                        img = document.createElement('img');
                        img.style.maxWidth = '200px';
                        img.style.maxHeight = '200px';
                        img.style.border = '1px solid #ddd';
                        img.style.borderRadius = '4px';
                        img.style.marginTop = '10px';
                        preview.appendChild(img);
                    }
                    
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>