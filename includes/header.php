<?php
// includes/header.php

// 1. Cargar Configuración y Funciones Globales
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda CUCEI - El Miguelito</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">
            <i class="fas fa-store me-2"></i>Marqueta CUCEI
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>catalogo.php">
                        <i class="fas fa-search me-1"></i> Catálogo
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center gap-2">
                
                <?php 
                $num_items = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;
                ?>
                <li class="nav-item me-2">
                    <a class="nav-link position-relative" href="<?php echo BASE_URL; ?>carrito/ver_carrito.php">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        <?php if($num_items > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                                <?php echo $num_items; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <div class="vr d-none d-lg-block mx-2 text-white opacity-50"></div>

                <?php if(is_logged_in()): ?>
                    <li class="nav-item dropdown d-flex align-items-center">
                        
                        <a class="nav-link d-flex align-items-center pe-1" href="<?php echo BASE_URL; ?>perfil/mis_pedidos.php" title="Ir a mi perfil">
                            <div class="bg-white text-primary rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 35px; height: 35px; font-weight: bold;">
                                <?php echo strtoupper(substr($_SESSION['user_nombre'], 0, 1)); ?>
                            </div>
                            <span class="d-none d-lg-block"><?php echo e($_SESSION['user_nombre']); ?></span>
                        </a>

                        <a class="nav-link dropdown-toggle dropdown-toggle-split ps-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Menú</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 animate__animated animate__fadeIn">
                            <li><h6 class="dropdown-header">Mi Cuenta</h6></li>
                            
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>perfil/mis_pedidos.php">
                                <i class="fas fa-user-circle me-2 text-primary"></i> Mi Perfil
                            </a></li>

                            <?php if($_SESSION['user_rol'] == 'vendedor'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header text-warning"><i class="fas fa-briefcase me-1"></i> Vendedor</h6></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>vendedor/dashboard.php">Panel de Control</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>vendedor/publicar.php">Publicar Producto</a></li>
                            <?php endif; ?>

                            <?php if($_SESSION['user_rol'] == 'admin'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>admin/dashboard.php">Panel Admin</a></li>
                            <?php endif; ?>

                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>auth/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                            </a></li>
                        </ul>
                    </li>

                <?php else: ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn btn-outline-light btn-sm rounded-pill px-3">Ingresar</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>auth/registro.php" class="btn btn-light btn-sm rounded-pill px-3 text-primary fw-bold">Registro</a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<main class="main-content container animate__animated animate__fadeIn">
    <?php displayMsg(); ?>