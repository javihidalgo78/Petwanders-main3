<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']));
    }

    $sql = "SELECT id, nombre, password FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];

            // Fetch cart for the user
            $cart_sql = "SELECT c.id_producto, c.cantidad, c.talla, p.nombre, p.precio, p.foto FROM carrito c JOIN productos p ON c.id_producto = p.id WHERE c.id_usuario = ?";
            $cart_stmt = $conn->prepare($cart_sql);
            $cart_stmt->bind_param('i', $usuario['id']);
            $cart_stmt->execute();
            $cart_result = $cart_stmt->get_result();

            $cart = [];
            while ($row = $cart_result->fetch_assoc()) {
                $cart[] = [
                    'id' => (int)$row['id_producto'],
                    'name' => $row['nombre'],
                    'price' => (float)$row['precio'],
                    'image' => 'Images/' . $row['foto'],
                    'size' => $row['talla'],
                    'quantity' => (int)$row['cantidad']
                ];
            }
            $cart_stmt->close();

            echo json_encode(['success' => true, 'message' => 'Login correcto', 'cart' => $cart]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    }

    $stmt->close();
    $conn->close();
}
?>