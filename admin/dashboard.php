<?php
// admin/dashboard.php
require_once __DIR__ . '/../includes/header.php';

// 1. SEGURIDAD: Solo administradores
if (!is_logged_in() || $_SESSION['user_rol'] !== 'admin') {
    redirect('../index.php');
}

$id_admin = $_SESSION['user_id'];

// -----------------------------
// ESTADÍSTICAS DEL ADMIN
// -----------------------------

// A. Total de usuarios
$stmt = $pdo->query("SELECT COUNT(*) FROM clientes");
$total_usuarios = $stmt->fetchColumn();

// B. Total de vendedores
$stmt = $pdo->query("SELECT COUNT(*) FROM clientes WHERE rol = 'vendedor'");
$total_vendedores = $stmt->fetchColumn();

// C. Productos publicados
$stmt = $pdo->query("SELECT COUNT(*) FROM productos WHERE eliminado = 0");
$total_productos = $stmt->fetchColumn();

// D. Reportes de publicaciones
$stmt = $pdo->query("SELECT COUNT(*) FROM reportes WHERE estado = 'pendiente'");
$total_reportes = $stmt->fetchColumn(); 
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">Panel de Administración</h2>
            <p class="text-muted">Bienvenido, <?php echo e($_SESSION['user_nombre']); ?></p>
        </div>
        <a href="../index.php" class="btn btn-secondary rounded-pill shadow-sm">
            <i class="fas fa-home me-2"></i> Volver al Inicio
        </a>
    </div>

    <div class="row g-4 mb-5">

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-primary fw-bold text-uppercase small">Usuarios Totales</div>
                        <i class="fas fa-users fa-2x text-primary opacity-25"></i>
                    </div>
                    <h2 class="fw-bold mb-3"><?php echo $total_usuarios; ?></h2>
                    <a href="usuarios.php" class="btn btn-sm btn-outline-primary rounded-pill w-100">Gestionar Usuarios</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-success fw-bold text-uppercase small">Vendedores</div>
                        <i class="fas fa-store fa-2x text-success opacity-25"></i>
                    </div>
                    <h2 class="fw-bold mb-3"><?php echo $total_vendedores; ?></h2>
                    <a href="vendedores.php" class="btn btn-sm btn-outline-success rounded-pill w-100">Ver Vendedores</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-warning fw-bold text-uppercase small">Productos Activos</div>
                        <i class="fas fa-tags fa-2x text-warning opacity-25"></i>
                    </div>
                    <h2 class="fw-bold mb-3"><?php echo $total_productos; ?></h2>
                    <a href="productos.php" class="btn btn-sm btn-outline-warning rounded-pill w-100">Ver Productos</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 border-start border-4 border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-danger fw-bold text-uppercase small">Reportes</div>
                        <i class="fas fa-flag fa-2x text-danger opacity-25"></i>
                    </div>

                    <h2 class="fw-bold mb-3"><?php echo $total_reportes; ?></h2>

                    <a href="reportes.php" class="btn btn-sm btn-outline-danger rounded-pill w-100">Revisar Reportes</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
