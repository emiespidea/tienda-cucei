<?php
// vendedor/mis_resenas.php
require_once __DIR__ . '/../includes/header.php';

if (!is_logged_in() || $_SESSION['user_rol'] !== 'vendedor') {
    redirect('index.php');
}

$id_vendedor = $_SESSION['user_id'];

// 1. Obtener reseñas
$sql = "SELECT r.*, p.nombre as producto_nombre, p.archivo, c.nombre as cliente_nombre 
        FROM resenas r
        JOIN productos p ON r.producto_id = p.id
        JOIN clientes c ON r.usuario_id = c.id
        WHERE p.vendedor_id = :vid
        ORDER BY r.fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['vid' => $id_vendedor]);
$resenas = $stmt->fetchAll();

// 2. Calcular promedio
$stmt_avg = $pdo->prepare("SELECT AVG(r.calificacion) FROM resenas r JOIN productos p ON r.producto_id = p.id WHERE p.vendedor_id = ?");
$stmt_avg->execute([$id_vendedor]);
$promedio = number_format((float)$stmt_avg->fetchColumn(), 1);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-primary">⭐ Opiniones de Clientes</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill btn-sm">Volver</a>
    </div>
    
    <div class="card border-0 shadow-sm rounded-pill px-4 py-2 d-flex flex-row align-items-center gap-3">
        <span class="text-muted small fw-bold text-uppercase">Calificación Global</span>
        <div class="d-flex align-items-center">
            <span class="h2 fw-bold text-warning mb-0 me-1"><?php echo $promedio; ?></span>
            <i class="fas fa-star text-warning"></i>
        </div>
    </div>
</div>

<?php if (empty($resenas)): ?>
    <div class="alert alert-light border shadow-sm rounded-4 text-center p-5">
        <h4 class="text-muted">Aún no tienes reseñas</h4>
        <p>¡Realiza ventas y pide a tus clientes que te califiquen!</p>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($resenas as $r): 
            $estrellas = str_repeat("⭐", $r['calificacion']);
            $img = $r['archivo'] ? "../uploads/productos/".$r['archivo'] : "https://via.placeholder.com/60";
        ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex gap-3">
                    <img src="<?php echo $img; ?>" class="rounded-3 border" style="width: 60px; height: 60px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold mb-0"><?php echo e($r['producto_nombre']); ?></h6>
                            <small class="text-muted"><?php echo date("d/m/y", strtotime($r['fecha'])); ?></small>
                        </div>
                        <div class="small text-muted mb-2">Cliente: <?php echo e($r['cliente_nombre']); ?></div>
                        
                        <div class="bg-light p-3 rounded-3">
                            <div class="mb-1 text-warning small"><?php echo $estrellas; ?></div>
                            <p class="mb-0 text-secondary fst-italic">"<?php echo e($r['comentario']); ?>"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>