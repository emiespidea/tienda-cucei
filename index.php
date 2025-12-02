<?php
// index.php
require_once __DIR__ . '/includes/header.php';

// 1. Obtener Productos Destacados (Los 6 más recientes con stock)
// Solo necesitamos esto, ya borramos la consulta de "promociones" porque no se usará.
$stmt_prod = $pdo->query("SELECT * FROM productos WHERE eliminado = 0 AND stock > 0 ORDER BY id DESC LIMIT 6");
$destacados = $stmt_prod->fetchAll();
?>

<section class="container my-5 animate__animated animate__fadeIn">
    
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">Novedades</h2>
            <p class="text-muted small">Lo último agregado por la comunidad</p>
        </div>
        <a href="catalogo.php" class="btn btn-outline-primary rounded-pill btn-sm">
            Ver todo <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>

    <?php if (empty($destacados)): ?>
        <div class="text-center py-5">
            <h4 class="text-muted">Aún no hay productos destacados.</h4>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php foreach ($destacados as $p): 
                $img = $p['archivo'] ? "uploads/productos/".$p['archivo'] : "https://via.placeholder.com/400x300";
                if(!file_exists("uploads/productos/" . $p['archivo'])) $img = "https://via.placeholder.com/400x300?text=Sin+Imagen";
            ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-4 product-card hover-shadow transition-all">
                    <div class="position-relative">
                        <img src="<?php echo $img; ?>" class="card-img-top" alt="<?php echo e($p['nombre']); ?>" style="height: 220px; object-fit: cover; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                        
                        <span class="badge bg-white text-dark position-absolute top-0 end-0 m-3 shadow-sm rounded-pill px-3 py-2 fw-bold">
                            $<?php echo number_format($p['precio'], 2); ?>
                        </span>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-dark mb-1 text-truncate"><?php echo e($p['nombre']); ?></h5>
                        <p class="card-text text-muted small flex-grow-1 text-truncate-2">
                            <?php echo e(isset($p['descripcion']) ? substr($p['descripcion'], 0, 60).'...' : ''); ?>
                        </p>
                        
                        <div class="d-grid mt-auto">
                            <a href="producto_detalle.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-primary rounded-pill fw-bold">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>

<section class="bg-white py-5 mt-5 border-top">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="p-3">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold">Seguridad Garantizada</h5>
                    <p class="text-muted small">Comunidad exclusiva con correo institucional.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 border-start border-end">
                    <i class="fas fa-handshake fa-3x text-success mb-3"></i>
                    <h5 class="fw-bold">Trato Directo</h5>
                    <p class="text-muted small">Acuerda entregas fáciles dentro del campus.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <i class="fas fa-search-dollar fa-3x text-warning mb-3"></i>
                    <h5 class="fw-bold">Mejores Precios</h5>
                    <p class="text-muted small">Materiales de segunda mano accesibles.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>