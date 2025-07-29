<?php
session_start();
require_once '../config/database.php';

// Verificar si el usuario está logueado y es un administrador
if (!isset($_SESSION['logueado']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Verificar el token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Error de CSRF");
}

if (isset($_POST['submit'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];

    // Manejo de la subida de la foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "../Images/";
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verificar si el archivo es una imagen real
        $check = getimagesize($_FILES["foto"]["tmp_name"]);
        if ($check !== false) {
            // Mover el archivo subido al directorio de imágenes
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $foto_nombre = basename($_FILES["foto"]["name"]);

                // Conectar a la base de datos
                $db = new Database();
                $conn = $db->getConexion();

                // Insertar el nuevo producto en la base de datos
                $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria, foto, disponible) VALUES (?, ?, ?, ?, ?, 1)");
                $stmt->bind_param("ssdss", $nombre, $descripcion, $precio, $categoria, $foto_nombre);

                if ($stmt->execute()) {
                    $_SESSION['mensaje'] = "Producto añadido con éxito.";
                } else {
                    $_SESSION['mensaje'] = "Error al añadir el producto: " . $stmt->error;
                }

                $stmt->close();
                $conn->close();
            } else {
                $_SESSION['mensaje'] = "Error al subir la foto.";
            }
        } else {
            $_SESSION['mensaje'] = "El archivo no es una imagen.";
        }
    } else {
        $_SESSION['mensaje'] = "Error al subir la foto.";
    }
} else {
    $_SESSION['mensaje'] = "No se ha enviado el formulario.";
}

header("Location: index.php");
exit();
?>