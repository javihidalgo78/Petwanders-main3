<?php
$host = $_SERVER['HTTP_HOST'];
$serverName = $_SERVER['SERVER_NAME'];

if (php_sapi_name() === 'cli' || $host === 'localhost' || 
    $host === '127.0.0.1' || 
    $serverName === 'localhost' || 
    $serverName === '127.0.0.1' ||
    strpos($host, 'localhost:') === 0 ||
    strpos($host, '127.0.0.1:') === 0) {
    
    //echo 'Entorno de desarrollo local';
    // Configuración para desarrollo
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'petwanders');
    define('URL_ADMIN', 'http://localhost/Petwanders/admin');
    define('BASE_URL', '/Petwanders/');
} else {
    //echo 'Entorno de producción en la nube';
    // Configuración para producción
    define("DB_HOST","db5018152607.hosting-data.io");
    define("DB_USER","dbu807028");
    define("DB_PASS","2102javI!");
    define("DB_NAME","dbs14399144");
    define('URL_ADMIN','http://www.javitxum.es/Petwanders/admin');
    define('BASE_URL', '/Petwanders/');
}

//credenciales para el envío de correos
define('MAIL_HOST', 'smtp.ionos.es');