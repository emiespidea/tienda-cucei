<?php
// perfil/detalle_pedido.php
require_once __DIR__ . '/../includes/header.php';

// 1. SEGURIDAD
if (!is_logged_in()) redirect('auth/login.php');

$pedido_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// 2. OBTENER INFO DEL PEDIDO (Verificando que pertenezca al usuario)
$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id = ? AND cliente_id = ?");
$stmt->execute([$pedido_id, $user_id]);
$pedido = $stmt->fetch();

if (!$pedido) {
    setMsg('danger', 'Pedido no encontrado o acceso denegado.');
    redirect('perfil/mis_pedidos.php');
}

// 3. OBTENER PRODUCTOS DEL PEDIDO
// Join con productos para obtener nombre e imagen
$sql_items = "SELECT dp.*, p.nombre, p.archivo, p.id as prod_id 
              FROM pedidos_productos dp 
              JOIN productos p ON dp.producto_id = p.id 
              WHERE dp.pedido_id = ?";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([$pedido_id]);
$items = $stmt_items->fetchAll();

// Estado visual
$estado = match((int)$pedido['status']) {
    0 => ['texto' => 'Pendiente', 'bg' => 'warning'],
    1 => ['texto' => 'Por Entregar', 'bg' => 'info'],
    2 => ['texto' => 'Pagado', 'bg' => 'primary'],
    3 => ['texto' => 'Entregado', 'bg' => 'success'],
    default => ['texto' => 'Desconocido', 'bg' => 'secondary']
};
?>

<div class="container mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">Detalle del Pedido #<?php echo str_pad($pedido['id'], 5, "0", STR_PAD_LEFT); ?></h2>
            <p class="text-muted mb-0">Fecha: <?php echo date("d/m/Y h:i A", strtotime($pedido['fecha'])); ?></p>
        </div>
        <a href="mis_pedidos.php" class="btn btn-outline-secondary rounded-pill btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <small class="text-muted fw-bold text-uppercase">Estado</small>
                    <div><span class="badge bg-<?php echo $estado['bg']; ?> rounded-pill px-3"><?php echo $estado['texto']; ?></span></div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted fw-bold text-uppercase">Método de Pago</small>
                    <div class="fw-bold"><?php echo e($pedido['metodo_pago']); ?></div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted fw-bold text-uppercase">Punto de Entrega</small>
                    <div class="small"><?php echo e($pedido['lugar_entrega']); ?></div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold text-secondary mb-3">Productos Comprados</h5>
    <div class="row g-3">
        <?php foreach($items as $item): 
             $img = $item['archivo'] ? "../uploads/productos/".$item['archivo'] : "https://via.placeholder.com/80";
             if(!file_exists(__DIR__ . "/../uploads/productos/" . $item['archivo'])) $img = "https://via.placeholder.com/80?text=Sin+Foto";
        ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?php echo $img; ?>" class="rounded-3 border" style="width: 70px; height: 70px; object-fit: cover;">
                        
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1"><?php echo e($item['nombre']); ?></h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><?php echo $item['cantidad']; ?> x $<?php echo number_format($item['precio'], 2); ?></small>
                                <span class="fw-bold text-success">$<?php echo number_format($item['cantidad'] * $item['precio'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-3 border-light">

                    <div class="d-grid">
                        <?php if($pedido['status'] == 3): ?>
                            <a href="../producto_detalle.php?id=<?php echo $item['prod_id']; ?>#seccion-opiniones" class="btn btn-warning btn-sm rounded-pill text-white fw-bold">
                                <i class="fas fa-star me-2"></i> Calificar / Opinar
                            </a>
                        <?php else: ?>
                            <button class="btn btn-light btn-sm rounded-pill text-muted" disabled>
                                Esperando entrega para calificar
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>