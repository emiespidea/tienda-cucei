<?php
// carrito/exito.php
require_once __DIR__ . '/../includes/header.php';
$folio = $_GET['folio'] ?? '---';
?>

<div class="row justify-content-center py-5">
    <div class="col-md-6 text-center">
        <div class="card border-0 shadow-lg rounded-4 p-5 animate__animated animate__zoomIn">
            
            <div class="mb-4">
                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center p-4" style="width: 120px; height: 120px;">
                    <i class="fas fa-check fa-4x text-success"></i>
                </div>
            </div>

            <h1 class="fw-bold text-success mb-3">¡Gracias por tu compra!</h1>
            <p class="text-muted mb-4 lead">
                Tu pedido ha sido registrado correctamente. <br>
                El vendedor recibirá una notificación para entregarte en el punto acordado.
            </p>

            <div class="bg-light p-3 rounded-3 mb-4 border border-success border-opacity-25 mx-auto" style="max-width: 300px;">
                <p class="mb-0 fw-bold text-dark">
                    <i class="fas fa-receipt me-2 text-success"></i>
                    Orden #<?php echo e($folio); ?>
                </p>
            </div>

            <div class="d-grid gap-2 col-lg-8 mx-auto">
                <a href="<?php echo BASE_URL; ?>perfil/mis_pedidos.php" class="btn btn-outline-primary rounded-pill">
                    <i class="fas fa-box-open me-2"></i> Ver mis pedidos
                </a>
                <a href="<?php echo BASE_URL; ?>catalogo.php" class="btn btn-primary rounded-pill shadow-sm">
                    <i class="fas fa-store me-2"></i> Seguir comprando
                </a>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>