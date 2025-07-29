<?php
session_start();

// Borrar todos los datos de la sesión
session_unset();

// Destruye la sesión
session_destroy();

// Redirigir al usuario a la página de login
header("Location: login.php");
exit();
?>