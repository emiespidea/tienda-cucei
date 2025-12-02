<?php
/* config/db.php */

// 1. Configuración de Base de Datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'proyecto1'); // Asegúrate que este sea el nombre real de tu BD en phpMyAdmin
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// 2. URL Base (IMPORTANTE)
// Cambia '/mi_tienda/' si tu carpeta tiene otro nombre.
define('BASE_URL', '/mi_tienda/'); 

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>