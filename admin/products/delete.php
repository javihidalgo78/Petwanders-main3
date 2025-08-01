<?php
/**
 * @file delete.php
 * @version 1.0
 * @author JHL
 * @brief Script para eliminar un producto de la base de datos.
 *
 * Este script recibe el ID de un producto a través de una solicitud GET,
 * se conecta a la base de datos y elimina el registro correspondiente
 * de la tabla `productos`.
 */

// Incluye el archivo de configuración para la conexión a la base de datos.
require_once '../../config/database.php';

// Crea una nueva conexión a la base de datos.
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
// Comprueba si hay errores en la conexión.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtiene el ID del producto desde la URL (parámetro GET).
$id = $_GET['id'];

// Prepara la consulta SQL para eliminar el producto con el ID especificado.
$sql = "DELETE FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);
// Vincula el ID como un entero para evitar inyección SQL.
$stmt->bind_param("i", $id);
// Ejecuta la consulta.
$stmt->execute();

// Cierra el statement y la conexión a la base de datos.
$stmt->close();
$conn->close();

// Redirige al usuario a la página principal de productos.
header("Location: index.php");
// Finaliza la ejecución del script para asegurar que la redirección se complete.
exit();
?>