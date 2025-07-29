<?php
require_once 'config/database.php';

$db = new Database();
$conexion = $db->getConexion();

$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$direccion = $_POST['direccion'];
$email = $_POST['email'];
$password = $_POST['password'];

// Validar datos (puedes añadir más validaciones)
if (empty($nombre) || empty($apellidos) || empty($direccion) || empty($email) || empty($password)) {
    die('Por favor, completa todos los campos.');
}

// Hashear la contraseña
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Preparar la consulta para evitar inyección SQL
$stmt = $conexion->prepare('INSERT INTO usuarios (nombre, apellidos, direccion, email, password) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sssss', $nombre, $apellidos, $direccion, $email, $hashed_password);

if ($stmt->execute()) {
    echo 'Usuario registrado exitosamente.';
} else {
    if ($conexion->errno === 1062) { // Error de entrada duplicada
        echo 'Error: El correo electrónico ya existe.';
    } else {
        echo 'Error al registrar el usuario: ' . $stmt->error;
    }
}

$stmt->close();
$db->close();
?>