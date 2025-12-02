<?php
// carrito/api_carrito.php

// 1. IMPORTANTE: Cargar configuración (db.php) PRIMERO para que exista BASE_URL
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$action = $_POST['action'] ?? '';

// --- 1. AGREGAR PRODUCTO ---
if ($action === 'add') {
    $id = (int)$_POST['producto_id'];
    $cantidad = (int)$_POST['cantidad'];
    $destino = $_POST['redireccion'] ?? 'carrito';

    if ($id > 0 && $cantidad > 0) {
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id] += $cantidad;
        } else {
            $_SESSION['carrito'][$id] = $cantidad;
        }
    }

    // Redirección inteligente
    if ($destino === 'checkout') {
        redirect('carrito/checkout.php');
    } elseif ($destino === 'producto') {
        // Ahora sí funcionará porque BASE_URL ya existe
        redirect("producto_detalle.php?id=$id&status=agregado");
    } else {
        redirect('carrito/ver_carrito.php');
    }
}

// --- 2. ACTUALIZAR CANTIDAD ---
if ($action === 'update') {
    $id = (int)$_POST['id'];
    $cantidad = (int)$_POST['cantidad'];

    if ($id > 0 && $cantidad > 0) {
        $_SESSION['carrito'][$id] = $cantidad;
    }
    redirect('carrito/ver_carrito.php');
}

// --- 3. ELIMINAR ITEM ---
if ($action === 'remove') {
    $id = (int)$_POST['id'];
    if (isset($_SESSION['carrito'][$id])) {
        unset($_SESSION['carrito'][$id]);
    }
    redirect('carrito/ver_carrito.php');
}

// --- 4. VACIAR CARRITO ---
if ($action === 'clear') {
    unset($_SESSION['carrito']);
    redirect('carrito/ver_carrito.php');
}

// Si no hay acción válida, volver
redirect('carrito/ver_carrito.php');
?>