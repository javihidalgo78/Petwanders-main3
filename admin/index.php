<?php
session_start();

// Verificar si el usuario está logueado y es un administrador
if (!isset($_SESSION['logueado']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Generar un token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Panel de Control de la Tienda</h1>
            <div>
                <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </header>

        <?php
        if (isset($_SESSION['mensaje'])) {
            echo '<div class="mensaje">' . htmlspecialchars($_SESSION['mensaje'], ENT_QUOTES, 'UTF-8') . '</div>';
            unset($_SESSION['mensaje']);
        }
        ?>

        <div class="panel-links">
            <a href="products/index.php" class="btn">Gestionar Productos</a>
            <a href="users/index.php" class="btn">Gestionar Usuarios</a>
        </div>
</body>
</html>