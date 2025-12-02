<?php
// carrito/checkout.php
require_once __DIR__ . '/../includes/header.php';

// Verificar login
if (!is_logged_in()) {
    setMsg('warning', 'Debes iniciar sesión para completar tu compra.');
    redirect('auth/login.php');
}

// Verificar carrito vacío
if (empty($_SESSION['carrito'])) {
    redirect('carrito/ver_carrito.php');
}

// Recalcular total rápido para mostrar en el botón
$total = 0;
$ids = array_keys($_SESSION['carrito']);
$placeholders = str_repeat('?,', count($ids) - 1) . '?';
$stmt = $pdo->prepare("SELECT id, precio FROM productos WHERE id IN ($placeholders)");
$stmt->execute($ids);
while($row = $stmt->fetch()){
    $total += $row['precio'] * $_SESSION['carrito'][$row['id']];
}
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <h2 class="fw-bold mb-4 text-center text-primary"><i class="fas fa-check-circle me-2"></i>Finalizar Compra</h2>
        
        <form action="finalizar.php" method="POST">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="mb-0 fw-bold text-primary">1. Datos de Entrega</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Lugar de Entrega (Dentro de CUCEI)</label>
                        <select name="lugar_entrega" class="form-select rounded-3" required>
                            <option value="Entrada Principal (Blvd. Marcelino)">Entrada Principal (Blvd. Marcelino)</option>
                            <option value="Entrada Olímpica">Entrada Olímpica</option>
                            <option value="Ciberjardín">Ciberjardín</option>
                            <option value="Rectoría">Rectoría</option>
                            <option value="Biblioteca">Biblioteca</option>
                            <option value="Módulos (Especificar en notas)">Módulos (Especificar en notas)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nota adicional / Horario</label>
                        <textarea name="nota" class="form-control rounded-3" rows="2" placeholder="Ej: Llevo playera roja, estoy en las mesas..."></textarea>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="mb-0 fw-bold text-primary">2. Método de Pago</h5>
                </div>
                <div class="card-body p-4">
                    
                    <div class="form-check p-3 border rounded-3 mb-3 bg-light cursor-pointer">
                        <input class="form-check-input mt-2" type="radio" name="metodo_pago" id="pago_efectivo" value="Efectivo" checked>
                        <label class="form-check-label w-100 d-flex justify-content-between align-items-center ms-2" for="pago_efectivo">
                            <span>
                                <i class="fas fa-money-bill-wave text-success me-2"></i> <strong>Efectivo contra entrega</strong>
                                <br><small class="text-muted ms-4">Pagas al recibir tu producto.</small>
                            </span>
                        </label>
                    </div>

                    <div class="form-check p-3 border rounded-3 bg-light">
                        <input class="form-check-input mt-2" type="radio" name="metodo_pago" id="pago_paypal" value="PayPal">
                        <label class="form-check-label w-100 d-flex justify-content-between align-items-center ms-2" for="pago_paypal">
                            <span>
                                <i class="fab fa-paypal text-primary me-2"></i> <strong>PayPal / Tarjeta</strong>
                                <br><small class="text-muted ms-4">Pago seguro en línea.</small>
                            </span>
                            <div>
                                <i class="fab fa-cc-visa fa-lg text-secondary mx-1"></i>
                                <i class="fab fa-cc-mastercard fa-lg text-secondary"></i>
                            </div>
                        </label>
                    </div>

                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg rounded-pill shadow fw-bold py-3">
                    Confirmar Pedido ($<?php echo number_format($total, 2); ?>)
                </button>
                <a href="ver_carrito.php" class="btn btn-link text-muted text-decoration-none text-center">Volver al carrito</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>