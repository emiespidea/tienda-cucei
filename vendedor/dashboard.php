<?php
// vendedor/dashboard.php
require_once __DIR__ . '/../includes/header.php';

// 1. SEGURIDAD: Solo vendedores
if (!is_logged_in() || $_SESSION['user_rol'] !== 'vendedor') {
    redirect('index.php');
}

$id_vendedor = $_SESSION['user_id'];

// 2. ESTADÍSTICAS (Consultas optimizadas PDO)
// A. Productos Activos
$stmt = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE vendedor_id = ? AND eliminado = 0");
$stmt->execute([$id_vendedor]);
$total_prod = $stmt->fetchColumn();

// B. Preguntas sin responder
$stmt = $pdo->prepare("SELECT COUNT(*) FROM preguntas p 
                       JOIN productos prod ON p.producto_id = prod.id 
                       WHERE prod.vendedor_id = ? AND p.respuesta IS NULL");
$stmt->execute([$id_vendedor]);
$total_preg = $stmt->fetchColumn();

// C. Ventas Pendientes (Status 1=Pendiente o 2=Pagado)
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT p.id) FROM pedidos_productos dp
                       JOIN pedidos p ON dp.pedido_id = p.id
                       WHERE dp.vendedor_id = ? AND (p.status = 1 OR p.status = 2)");
$stmt->execute([$id_vendedor]);
$total_ventas = $stmt->fetchColumn();

// D. Calificación Promedio
$stmt = $pdo->prepare("SELECT AVG(r.calificacion) FROM resenas r 
                       JOIN productos p ON r.producto_id = p.id 
                       WHERE p.vendedor_id = ?");
$stmt->execute([$id_vendedor]);
$promedio = number_format((float)$stmt->fetchColumn(), 1);
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">Panel de Vendedor</h2>
            <p class="text-muted">Bienvenido, <?php echo e($_SESSION['user_nombre']); ?></p>
        </div>
        <a href="publicar.php" class="btn btn-primary rounded-pill shadow-sm">
            <i class="fas fa-plus me-2"></i> Nuevo Producto
        </a>
    </div>

    <div class="row g-4 mb-5">
        
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-success fw-bold text-uppercase small">Pedidos Pendientes</div>
                        <i class="fas fa-box fa-2x text-success opacity-25"></i>
                    </div>
                    <h2 class="fw-bold mb-3"><?php echo $total_ventas; ?></h2>
                    <a href="pedidos.php" class="btn btn-sm btn-outline-success rounded-pill w-100">Gestionar Envíos</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 border-start border-4 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-info fw-bold text-uppercase small">Preguntas Nuevas</div>
                        <i class="fas fa-comments fa-2x text-info opacity-25"></i>
                    </div>
                    <h2 class="fw-bold mb-3"><?php echo $total_preg; ?></h2>
                    <a href="mis_preguntas.php" class="btn btn-sm btn-outline-info rounded-pill w-100">Responder</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-warning fw-bold text-uppercase small">Mis Productos</div>
                        <i class="fas fa-tags fa-2x text-warning opacity-25"></i>
                    </div>
                    <h2 class="fw-bold mb-3"><?php echo $total_prod; ?></h2>
                    <a href="mis_productos.php" class="btn btn-sm btn-outline-warning rounded-pill w-100">Ver Inventario</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-4 border-start border-4 border-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-secondary fw-bold text-uppercase small">Calificación</div>
                        <i class="fas fa-star fa-2x text-secondary opacity-25"></i>
                    </div>
                    <h2 class="fw-bold mb-3"><?php echo $promedio; ?> <span class="fs-6 text-muted">/ 5.0</span></h2>
                    <a href="mis_resenas.php" class="btn btn-sm btn-outline-secondary rounded-pill w-100">Ver Opiniones</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>