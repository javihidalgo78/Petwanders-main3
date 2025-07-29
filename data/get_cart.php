<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die(json_encode([]));
    }

    $sql = "SELECT c.id_producto, c.cantidad, c.talla, p.nombre, p.precio, p.foto FROM carrito c JOIN productos p ON c.id_producto = p.id WHERE c.id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart = [];
    while ($row = $result->fetch_assoc()) {
        $cart[] = [
            'id' => (int)$row['id_producto'],
            'name' => $row['nombre'],
            'price' => (float)$row['precio'],
            'image' => 'Images/' . $row['foto'],
            'size' => $row['talla'],
            'quantity' => (int)$row['cantidad']
        ];
    }

    echo json_encode($cart);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([]);
}
?>