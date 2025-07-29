<?php
require_once '../config/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = new Database();
    $conn = $db->getConexion();

    // Usar sentencias preparadas para prevenir inyección SQL
    $stmt = $conn->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña y el rol
        if (password_verify($password, $user['password']) && $user['rol'] === 'admin') {
            // Iniciar sesión
            $_SESSION['logueado'] = true;
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];

            // Redirigir al panel de administración
            header("Location: index.php");
            exit();
        } else {
            // Contraseña incorrecta o no es admin
            $_SESSION['mensaje'] = "Credenciales incorrectas o no tienes permiso de administrador.";
            header("Location: login.php");
            exit();
        }
    } else {
        // Usuario no encontrado
        $_SESSION['mensaje'] = "No se encontró ningún usuario con ese email.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirigir si no es una solicitud POST
    header("Location: login.php");
    exit();
}
?>