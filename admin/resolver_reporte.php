<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
session_start();

if (!is_logged_in() || $_SESSION['user_rol'] !== 'admin') {
    redirect('../index.php');
}

$id = $_GET['id'] ?? null;


$return = 'admin/reportes.php';

if ($id) {
    $stmt = $pdo->prepare("UPDATE reportes SET estado='revisado' WHERE id=?");
    $stmt->execute([$id]);
    setMsg('success', 'Reporte marcado como revisado.');
}

redirect($return);
exit;

