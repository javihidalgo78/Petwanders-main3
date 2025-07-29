<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['usuario_id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $cart = json_decode(file_get_contents('php://input'), true);

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']));
    }

    // Limpiar el carrito actual del usuario
    $sql = "DELETE FROM carrito WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();

    // Insertar los nuevos productos del carrito
    $sql = "INSERT INTO carrito (id_usuario, id_producto, cantidad, talla) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    foreach ($cart as $item) {
        $stmt->bind_param('iiis', $usuario_id, $item['id'], $item['quantity'], $item['size']);
        $stmt->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Carrito guardado']);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
}
?>