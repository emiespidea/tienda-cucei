<?php
// catalogo.php
require_once __DIR__ . '/includes/header.php';

// --- CONSTRUCCIÓN DE FILTROS ---
$params = [];
// AQUÍ ESTÁ EL CAMBIO: Agregamos "stock > 0"
$where_clauses = ["eliminado = 0", "stock > 0"];

// 1. Búsqueda Texto
$q = trim($_GET['q'] ?? '');
if (!empty($q)) {
    $where_clauses[] = "(nombre LIKE ? OR descripcion LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

// 2. Filtro Precio
$min = isset($_GET['min']) && $_GET['min'] !== '' ? (float)$_GET['min'] : null;
$max = isset($_GET['max']) && $_GET['max'] !== '' ? (float)$_GET['max'] : null;

if ($min !== null) {
    $where_clauses[] = "precio >= ?";
    $params[] = $min;
}
if ($max !== null) {
    $where_clauses[] = "precio <= ?";
    $params[] = $max;
}

// 3. Ordenamiento
$sort_options = [
    'nuevo' => 'id DESC',
    'precio_asc' => 'precio ASC',
    'precio_desc' => 'precio DESC',
    'nombre_asc' => 'nombre ASC'
];
$orden = $_GET['orden'] ?? 'nuevo';
$order_sql = $sort_options[$orden] ?? 'id DESC';

// --- EJECUTAR CONSULTA ---
$sql = "SELECT * FROM productos WHERE " . implode(" AND ", $where_clauses) . " ORDER BY " . $order_sql;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll();
?>

<div class="container mb-5">
    <div class="text-center mb-5 animate__animated animate__fadeIn">
        <h1 class="fw-bold text-primary">Catálogo Completo</h1>
        <p class="text-muted">Explora todo lo que la comunidad tiene para ofrecer</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px; z-index: 10;">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-filter me-2 text-primary"></i>Filtros</h5>
                <form action="catalogo.php" method="GET">
                    <?php if(!empty($q)): ?>
                        <div class="alert alert-primary py-2 px-3 small rounded-3 mb-3 d-flex justify-content-between align-items-center">
                            <span>"<?php echo e($q); ?>"</span>
                            <a href="catalogo.php" class="text-primary"><i class="fas fa-times"></i></a>
                        </div>
                        <input type="hidden" name="q" value="<?php echo e($q); ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Precio</label>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text">$</span>
                            <input type="number" name="min" class="form-control" placeholder="Mín" value="<?php echo $min; ?>">
                            <span class="input-group-text">-</span>
                            <input type="number" name="max" class="form-control" placeholder="Máx" value="<?php echo $max; ?>">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Ordenar por</label>
                        <select name="orden" class="form-select form-select-sm rounded-3">
                            <option value="nuevo" <?php echo $orden == 'nuevo' ? 'selected' : ''; ?>>Más Recientes</option>
                            <option value="precio_asc" <?php echo $orden == 'precio_asc' ? 'selected' : ''; ?>>Precio: Bajo a Alto</option>
                            <option value="precio_desc" <?php echo $orden == 'precio_desc' ? 'selected' : ''; ?>>Precio: Alto a Bajo</option>
                            <option value="nombre_asc" <?php echo $orden == 'nombre_asc' ? 'selected' : ''; ?>>Nombre (A-Z)</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill btn-sm fw-bold">Aplicar</button>
                        <a href="catalogo.php" class="btn btn-outline-secondary rounded-pill btn-sm">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-9">
            <form action="catalogo.php" method="GET" class="mb-4">
                <div class="input-group shadow-sm rounded-pill overflow-hidden">
                    <span class="input-group-text bg-white border-0 ps-4"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-0 py-3" placeholder="¿Qué buscas hoy?" value="<?php echo e($q); ?>">
                    <button class="btn btn-primary px-4 fw-bold" type="submit">Buscar</button>
                </div>
            </form>

            <?php if (empty($productos)): ?>
                <div class="text-center py-5">
                    <div class="mb-3 opacity-25"><i class="fas fa-ghost fa-4x text-muted"></i></div>
                    <h4 class="text-muted fw-bold">No se encontraron resultados</h4>
                    <p class="text-muted">Intenta con otros filtros.</p>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    <?php foreach ($productos as $p): 
                        $img = $p['archivo'] ? "uploads/productos/".$p['archivo'] : "https://via.placeholder.com/400x300";
                        if(!file_exists("uploads/productos/" . $p['archivo'])) $img = "https://via.placeholder.com/400x300?text=Sin+Imagen";
                    ?>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-4 hover-shadow transition-all">
                            <a href="producto_detalle.php?id=<?php echo $p['id']; ?>" class="text-decoration-none text-dark">
                                <img src="<?php echo $img; ?>" class="card-img-top" style="height: 200px; object-fit: cover; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-1 text-truncate"><?php echo e($p['nombre']); ?></h5>
                                    <p class="fw-bold text-primary fs-5 mb-2">$<?php echo number_format($p['precio'], 2); ?></p>
                                    <div class="d-grid">
                                        <span class="btn btn-sm btn-outline-primary rounded-pill">Ver Detalle</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>