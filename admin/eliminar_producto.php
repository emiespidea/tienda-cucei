<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
session_start();

if (!is_logged_in() || $_SESSION['user_rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'] ?? null;
$from = $_GET['from'] ?? 'productos'; 
$id_v = $_GET['id_v'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("UPDATE productos SET eliminado = 1 WHERE id = ?");
    $stmt->execute([$id]);
}

if ($from === 'vendedor' && $id_v) {
    header("Location: vendedor_productos.php?id=$id_v");
    exit;
}

header("Location: productos.php");
exit;
