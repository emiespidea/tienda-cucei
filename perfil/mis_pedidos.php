<?php
// perfil/mis_pedidos.php
require_once __DIR__ . '/../includes/header.php';

// Seguridad: Verificar Login
if (!is_logged_in()) {
    redirect('auth/login.php');
}

$user_id = $_SESSION['user_id'];

// 1. Obtener datos frescos del usuario (por si cambió algo en la BD)
$stmt_user = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
$stmt_user->execute(['id' => $user_id]);
$user = $stmt_user->fetch();

// 2. Obtener historial de pedidos con totales calculados
// Usamos LEFT JOIN para sumar los productos de cada pedido
$sql_pedidos = "SELECT p.id, p.fecha, p.status, p.metodo_pago,
                       COALESCE(SUM(pp.cantidad * pp.precio), 0) as total
                FROM pedidos p
                LEFT JOIN pedidos_productos pp ON p.id = pp.pedido_id
                WHERE p.cliente_id = :uid
                GROUP BY p.id
                ORDER BY p.fecha DESC";

$stmt_pedidos = $pdo->prepare($sql_pedidos);
$stmt_pedidos->execute(['uid' => $user_id]);
$pedidos = $stmt_pedidos->fetchAll();
?>

<div class="row g-4">
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
            <div class="mb-4 mt-2">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 100px; height: 100px; font-size: 2.5em;">
                    <?php echo strtoupper(substr($user['nombre'], 0, 1)); ?>
                </div>
            </div>
            
            <h4 class="fw-bold text-dark mb-1"><?php echo e($user['nombre'] . ' ' . $user['apellidos']); ?></h4>
            <p class="text-muted mb-3"><i class="fas fa-envelope me-2"></i><?php echo e($user['correo']); ?></p>
            
            <div class="mb-4">
                <span class="badge rounded-pill bg-info text-dark px-3 py-2 text-uppercase shadow-sm">
                    <?php echo e($user['rol']); ?>
                </span>
            </div>

            <hr class="my-4">
            
            <div class="d-grid gap-2">
                <a href="<?php echo BASE_URL; ?>auth/logout.php" class="btn btn-outline-danger rounded-pill hover-scale">
                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white border-bottom border-light py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-history me-2"></i>Historial de Pedidos</h5>
                <span class="badge bg-light text-dark border"><?php echo count($pedidos); ?> Registros</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3"># Orden</th>
                            <th>Fecha</th>
                            <th>Método</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pedidos)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-3 opacity-25">
                                        <i class="fas fa-box-open fa-3x"></i>
                                    </div>
                                    <p class="mb-2">Aún no has realizado compras.</p>
                                    <a href="<?php echo BASE_URL; ?>catalogo.php" class="btn btn-sm btn-primary rounded-pill px-3">
                                        Ir al catálogo
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pedidos as $ped): 
                                // Lógica visual de estados con 'match' (PHP 8+)
                                $estado_badge = match((int)$ped['status']) {
                                    0 => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pendiente</span>',
                                    1 => '<span class="badge bg-info text-dark"><i class="fas fa-truck me-1"></i> Por Entregar</span>',
                                    2 => '<span class="badge bg-primary"><i class="fab fa-paypal me-1"></i> Pagado</span>',
                                    3 => '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Entregado</span>',
                                    default => '<span class="badge bg-secondary">Desconocido</span>'
                                };
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    #<?php echo str_pad($ped['id'], 5, "0", STR_PAD_LEFT); ?>
                                </td>
                                <td class="text-secondary small">
                                    <?php echo date("d/m/Y", strtotime($ped['fecha'])); ?>
                                </td>
                                <td class="small text-muted">
                                    <?php echo e($ped['metodo_pago']); ?>
                                </td>
                                <td class="fw-bold text-success">
                                    $<?php echo number_format($ped['total'], 2); ?>
                                </td>
                                <td>
                                    <?php echo $estado_badge; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>