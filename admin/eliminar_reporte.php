<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
session_start();

// Seguridad
if (!is_logged_in() || $_SESSION['user_rol'] !== 'admin') {
    redirect('../index.php');
}

// Obtener ID del reporte
$id = $_GET['id'] ?? null;

$return = 'admin/reportes.php';

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM reportes WHERE id = ?");
    $stmt->execute([$id]);
    setMsg('success', 'Reporte eliminado correctamente.');
}

redirect($return);
exit;
