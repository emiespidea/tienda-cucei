<?php
// vendedor/mis_productos.php

// 1. CARGAR DEPENDENCIAS MANUALMENTE (Sin HTML todavía)
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// 2. SEGURIDAD
if (!is_logged_in() || $_SESSION['user_rol'] !== 'vendedor') {
    redirect('index.php');
}

$id_vendedor = $_SESSION['user_id'];

// 3. LÓGICA DE ELIMINAR (Esto debe ir ANTES del header)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id_a_borrar = (int)$_POST['eliminar_id'];
    
    // "Eliminado lógico" (no borramos el registro, solo lo ocultamos)
    $stmt = $pdo->prepare("UPDATE productos SET eliminado = 1 WHERE id = :pid AND vendedor_id = :vid");
    
    if ($stmt->execute(['pid' => $id_a_borrar, 'vid' => $id_vendedor])) {
        setMsg('warning', 'Producto eliminado correctamente.');
        // Ahora la redirección funcionará porque no hay HTML impreso
        redirect('vendedor/mis_productos.php');
    }
}

// 4. CONSULTA DE PRODUCTOS (También antes del HTML para tener los datos listos)
$stmt = $pdo->prepare("SELECT * FROM productos WHERE vendedor_id = ? AND eliminado = 0 ORDER BY id DESC");
$stmt->execute([$id_vendedor]);
$productos = $stmt->fetchAll();

// 5. AHORA SÍ: CARGAR LA VISTA (HEADER HTML)
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">📦 Mi Inventario</h2>
    <a href="publicar.php" class="btn btn-primary rounded-pill shadow-sm">
        <i class="fas fa-plus me-2"></i> Publicar
    </a>
</div>

<?php if (empty($productos)): ?>
    <div class="text-center p-5 bg-white rounded-4 shadow-sm">
        <h4 class="text-muted">Tu inventario está vacío</h4>
        <p class="text-muted mb-4">Empieza a vender publicando tu primer producto.</p>
        <a href="publicar.php" class="btn btn-primary rounded-pill">Publicar Ahora</a>
    </div>
<?php else: ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Producto</th>
                        <th>Código (SKU)</th>
                        <th class="text-center">Stock</th>
                        <th>Precio</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): 
                        $img = $p['archivo'] ? "../uploads/productos/".$p['archivo'] : "https://via.placeholder.com/60";
                        if(!file_exists(__DIR__ . "/../uploads/productos/" . $p['archivo'])) $img = "https://via.placeholder.com/60?text=Sin+Foto";
                    ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $img; ?>" class="rounded-3 me-3 border" style="width: 50px; height: 50px; object-fit: cover;">
                                <span class="fw-bold text-dark"><?php echo e($p['nombre']); ?></span>
                            </div>
                        </td>
                        <td class="small text-muted"><?php echo e($p['codigo']); ?></td>
                        <td class="text-center">
                            <?php if ($p['stock'] < 5): ?>
                                <span class="badge bg-danger rounded-pill"><?php echo $p['stock']; ?></span>
                            <?php else: ?>
                                <span class="badge bg-success rounded-pill"><?php echo $p['stock']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold text-primary">$<?php echo number_format($p['precio'], 2); ?></td>
                        <td class="text-end pe-4">
                            <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                                <input type="hidden" name="eliminar_id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle border-0" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>