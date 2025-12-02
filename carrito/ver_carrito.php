<?php
// carrito/ver_carrito.php
require_once __DIR__ . '/../includes/header.php';

$carrito = $_SESSION['carrito'] ?? [];
$productos_en_carrito = [];
$total = 0;

// Si hay items, obtener sus datos frescos de la BD
if (!empty($carrito)) {
    // Generar placeholders para la query IN (?,?,?)
    $ids = array_keys($carrito);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $productos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mapear resultados para fácil acceso
    foreach ($productos_db as $p) {
        $id = $p['id'];
        $cantidad = $carrito[$id];
        $subtotal = $p['precio'] * $cantidad;
        $total += $subtotal;
        
        // Agregamos la cantidad que tenemos en sesión a los datos de la BD
        $p['cantidad_carrito'] = $cantidad;
        $p['subtotal'] = $subtotal;
        $productos_en_carrito[] = $p;
    }
}
?>

<div class="row">
    <div class="col-12 mb-4">
        <h2 class="fw-bold text-primary"><i class="fas fa-shopping-cart me-2"></i>Tu Carrito</h2>
    </div>

    <?php if (empty($productos_en_carrito)): ?>
        <div class="col-12">
            <div class="card shadow-sm text-center p-5 rounded-4">
                <div class="mb-3 text-muted opacity-25">
                    <i class="fas fa-shopping-basket fa-4x"></i>
                </div>
                <h3 class="text-muted fw-bold">Tu carrito está vacío</h3>
                <p class="text-muted mb-4">Parece que no has agregado nada aún.</p>
                <div>
                    <a href="<?php echo BASE_URL; ?>catalogo.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i> Ir al Catálogo
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>

        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-primary">
                            <tr>
                                <th class="ps-4 py-3">Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end pe-4">Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos_en_carrito as $prod): 
                                $img = $prod['archivo'] ? "../uploads/productos/".$prod['archivo'] : "https://via.placeholder.com/80";
                                if(!file_exists(__DIR__ . "/../uploads/productos/" . $prod['archivo'])) $img = "https://via.placeholder.com/80?text=Sin+Foto";
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo $img; ?>" class="rounded-3 me-3 border" style="width: 60px; height: 60px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark"><?php echo e($prod['nombre']); ?></h6>
                                            <small class="text-muted">$<?php echo number_format($prod['precio'], 2); ?> c/u</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center bg-light rounded-pill border px-2">
                                        
                                        <form action="api_carrito.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                                            <input type="hidden" name="cantidad" value="<?php echo max(1, $prod['cantidad_carrito'] - 1); ?>">
                                            <button type="submit" class="btn btn-link btn-sm text-decoration-none text-muted p-0"><i class="fas fa-minus small"></i></button>
                                        </form>

                                        <span class="mx-3 fw-bold small"><?php echo $prod['cantidad_carrito']; ?></span>

                                        <form action="api_carrito.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                                            <input type="hidden" name="cantidad" value="<?php echo min($prod['stock'], $prod['cantidad_carrito'] + 1); ?>">
                                            <button type="submit" class="btn btn-link btn-sm text-decoration-none text-muted p-0"><i class="fas fa-plus small"></i></button>
                                        </form>
                                    </div>
                                    <div class="small text-muted mt-1">Disp: <?php echo $prod['stock']; ?></div>
                                </td>
                                <td class="text-end pe-4 fw-bold text-dark fs-5">
                                    $<?php echo number_format($prod['subtotal'], 2); ?>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="api_carrito.php" method="POST">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                                        <button class="btn btn-sm btn-outline-danger rounded-circle border-0" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white py-3">
                    <form action="api_carrito.php" method="POST" class="d-inline">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill">
                            <i class="fas fa-trash me-1"></i> Vaciar Carrito
                        </button>
                    </form>
                </div>
            </div>
            
            <a href="<?php echo BASE_URL; ?>catalogo.php" class="text-decoration-none text-muted ms-2">
                <i class="fas fa-arrow-left me-1"></i> Seguir comprando
            </a>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-4 text-primary">Resumen del Pedido</h5>
                
                <div class="d-flex justify-content-between mb-2 text-secondary">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 text-secondary">
                    <span>Envío (Campus)</span>
                    <span class="text-success fw-bold">Gratis</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4 align-items-center">
                    <span class="h5 fw-bold mb-0">Total</span>
                    <span class="h3 fw-bold text-primary mb-0">$<?php echo number_format($total, 2); ?></span>
                </div>

                <a href="checkout.php" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm">
                    Proceder al Pago <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>