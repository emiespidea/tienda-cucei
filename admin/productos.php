<?php
require_once __DIR__ . '/../includes/header.php';

// Seguridad: solo admin
if (!is_logged_in() || $_SESSION['user_rol'] !== 'admin') {
    redirect('../index.php');
}

// Consulta correcta según DB real
$stmt = $pdo->query("
    SELECT 
        p.id,
        p.vendedor_id,
        c.nombre AS vendedor,
        p.nombre AS producto,
        p.precio,
        p.stock,
        p.eliminado
    FROM productos p
    JOIN clientes c ON p.vendedor_id = c.id
    ORDER BY p.id ASC
");

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <h2 class="fw-bold text-primary mb-4">Gestión de Productos</h2>

    <table class="table table-striped table-hover shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Vendedor</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo e($p['vendedor']); ?></td>
                <td><?php echo e($p['producto']); ?></td>
                <td>$<?php echo number_format($p['precio'], 2); ?></td>
                <td><?php echo $p['stock']; ?></td>

                <!-- Estado -->
                <td>
                    <?php if ($p['eliminado'] == 1): ?>
                        <span class="badge bg-danger">Eliminado</span>
                    <?php else: ?>
                        <span class="badge bg-success">Activo</span>
                    <?php endif; ?>
                </td>

                <!-- BOTONES DE ACCIÓN -->
                <td>

                    <!-- Ver detalle -->
                    <a href="ver_producto.php?id=<?= $p['id']; ?>&return=productos.php"
                       class="btn btn-sm btn-info">
                       Ver detalle
                    </a>

                    <!-- Restaurar -->
                    <?php if ($p['eliminado'] == 1): ?>

                        <a href="restaurar_producto.php?id=<?= $p['id']; ?>&return=productos.php"
                           class="btn btn-sm btn-success">
                           Restaurar
                        </a>

                    <!-- Eliminar -->
                    <?php else: ?>

                        <a href="eliminar_producto.php?id=<?= $p['id']; ?>&from=productos"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Eliminar este producto?');">
                           Eliminar
                        </a>

                    <?php endif; ?>

                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Volver al Panel</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


