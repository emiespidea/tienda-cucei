<?php
require_once __DIR__ . '/../includes/header.php';

if (!is_logged_in() || $_SESSION['user_rol'] !== 'admin') {
    redirect('../index.php');
}

// Obtener ID del producto
$id = $_GET['id'] ?? null;

// Obtener URL de retorno
$return = $_GET['return'] ?? 'productos.php';

if (!$id) {
    redirect($return);
}

// Obtener datos del producto
$stmt = $pdo->prepare("
    SELECT p.*, c.nombre AS vendedor_nombre, c.apellidos AS vendedor_apellidos
    FROM productos p
    JOIN clientes c ON c.id = p.vendedor_id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    redirect($return);
}

?>

<div class="container my-5">
    <h2 class="fw-bold text-primary mb-4">Detalle del Producto</h2>

    <div class="card shadow-sm p-4">

        <h4 class="fw-bold mb-3"><?= e($producto['nombre']); ?></h4>

        <p><strong>ID:</strong> <?= $producto['id']; ?></p>
        <p><strong>Vendedor:</strong> <?= e($producto['vendedor_nombre'] . ' ' . $producto['vendedor_apellidos']); ?></p>
        <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2); ?></p>
        <p><strong>Stock:</strong> <?= $producto['stock']; ?></p>
        <p><strong>Código:</strong> <?= e($producto['codigo']); ?></p>

        <p><strong>Descripción:</strong><br><?= nl2br(e($producto['descripcion'])); ?></p>

        <?php if (!empty($producto['archivo'])): ?>
            <p class="mt-3"><strong>Imagen:</strong></p>

            <?php 
                $ruta = "../uploads/productos/" . $producto['archivo'];
            ?>

            <?php if (file_exists($ruta)): ?>
                <img src="<?= $ruta ?>" 
                    alt="Imagen del producto" 
                    class="img-fluid rounded shadow"
                    style="max-width: 300px;">
            <?php else: ?>
                <p class="text-danger fw-bold">⚠ No se encontró la imagen en /uploads/productos</p>
                <p>Busqué: <code><?= $ruta ?></code></p>
            <?php endif; ?>

        <?php endif; ?>

        <div class="mt-4">
        <?php
        $return = $_GET['return'] ?? 'admin/productos.php';

        if (strpos($return, 'admin/') !== 0) {
            $return = 'admin/' . $return;
        }
        ?>
            <a href="<?= BASE_URL . $return ?>" class="btn btn-secondary">Regresar</a>
        </div>
        
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
