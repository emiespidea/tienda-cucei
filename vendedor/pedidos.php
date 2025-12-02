<?php
// vendedor/pedidos.php
require_once __DIR__ . '/../includes/header.php';

if (!is_logged_in() || $_SESSION['user_rol'] !== 'vendedor') {
    redirect('index.php');
}

$id_vendedor = $_SESSION['user_id'];

// --- LÓGICA DE ACTUALIZAR STATUS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_entregado_id'])) {
    $pedido_id = (int)$_POST['marcar_entregado_id'];
    // Actualizamos el status a 3 (Entregado)
    $stmt = $pdo->prepare("UPDATE pedidos SET status = 3 WHERE id = :pid");
    if ($stmt->execute(['pid' => $pedido_id])) {
        setMsg('success', 'Pedido marcado como entregado.');
        redirect('vendedor/pedidos.php');
    }
}

// --- CONSULTA COMPLEJA ---
// Trae los datos del pedido, del cliente y del producto específico de este vendedor
$sql = "SELECT p.id as pedido_id, p.fecha, p.status, p.metodo_pago, p.lugar_entrega,
               c.nombre as cliente, c.correo,
               dp.cantidad, dp.precio as precio_venta,
               prod.nombre as producto, prod.archivo
        FROM pedidos_productos dp
        JOIN pedidos p ON dp.pedido_id = p.id
        JOIN productos prod ON dp.producto_id = prod.id
        JOIN clientes c ON p.cliente_id = c.id
        WHERE dp.vendedor_id = :vid
        ORDER BY p.fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['vid' => $id_vendedor]);
$pedidos = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">📦 Gestión de Envíos</h2>
    <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill btn-sm">Volver al Panel</a>
</div>

<?php if (empty($pedidos)): ?>
    <div class="alert alert-info rounded-4 border-0 shadow-sm">
        <i class="fas fa-info-circle me-2"></i> No tienes ventas registradas aún.
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($pedidos as $row): 
             // Determinar color y texto según status
             $estado = match((int)$row['status']) {
                 1 => ['color' => 'warning', 'texto' => '⏳ Pendiente'],
                 2 => ['color' => 'primary', 'texto' => '✅ Pagado (PayPal)'],
                 3 => ['color' => 'success', 'texto' => '✓ Finalizado'],
                 default => ['color' => 'secondary', 'texto' => 'Desconocido']
             };
             $img = $row['archivo'] ? "../uploads/productos/".$row['archivo'] : "https://via.placeholder.com/80";
        ?>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center border-0 pt-3 px-4">
                        <span class="fw-bold">Pedido #<?php echo str_pad($row['pedido_id'], 5, "0", STR_PAD_LEFT); ?></span>
                        <span class="badge bg-<?php echo $estado['color']; ?> bg-opacity-10 text-<?php echo $estado['color']; ?> px-3 py-2 rounded-pill">
                            <?php echo $estado['texto']; ?>
                        </span>
                    </div>
                    
                    <div class="card-body px-4">
                        <div class="d-flex align-items-start mb-3">
                            <img src="<?php echo $img; ?>" class="rounded-3 border me-3" style="width: 70px; height: 70px; object-fit: cover;">
                            <div>
                                <h6 class="fw-bold mb-1"><?php echo e($row['producto']); ?></h6>
                                <p class="mb-0 text-muted small">Cantidad: <strong><?php echo $row['cantidad']; ?></strong></p>
                                <p class="mb-0 text-success fw-bold">Cobrar: $<?php echo number_format($row['cantidad'] * $row['precio_venta'], 2); ?></p>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded-3 mb-3">
                            <div class="small text-muted text-uppercase fw-bold mb-1">Cliente</div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user-circle me-2 text-secondary"></i>
                                <span><?php echo e($row['cliente']); ?></span>
                            </div>
                            <div class="small text-muted text-uppercase fw-bold mb-1 mt-2">Punto de Encuentro</div>
                            <div class="d-flex align-items-start">
                                <i class="fas fa-map-marker-alt me-2 text-danger mt-1"></i>
                                <span class="small text-dark"><?php echo e($row['lugar_entrega']); ?></span>
                            </div>
                        </div>

                        <?php if ($row['status'] != 3): ?>
                            <form method="POST">
                                <input type="hidden" name="marcar_entregado_id" value="<?php echo $row['pedido_id']; ?>">
                                <button type="submit" class="btn btn-success w-100 rounded-pill shadow-sm" onclick="return confirm('¿Confirmas que ya entregaste el producto?');">
                                    <i class="fas fa-check-double me-2"></i> Marcar como Entregado
                                </button>
                            </form>
                        <?php else: ?>
                             <button class="btn btn-secondary w-100 rounded-pill disabled" disabled>Entregado</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>