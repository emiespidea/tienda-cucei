<?php
// producto_detalle.php

// 1. CARGA DEPENDENCIAS
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$id_producto = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_producto <= 0) redirect('catalogo.php');

// --- 2. PROCESAR FORMULARIOS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    if (isset($_POST['nueva_pregunta'])) {
        $pregunta = trim($_POST['pregunta']);
        if (!empty($pregunta)) {
            $stmt = $pdo->prepare("INSERT INTO preguntas (producto_id, usuario_id, pregunta, fecha) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$id_producto, $_SESSION['user_id'], $pregunta]);
            setMsg('success', 'Pregunta enviada.');
        }
        redirect("producto_detalle.php?id=$id_producto");
    }
    if (isset($_POST['nueva_resena'])) {
        $calif = (int)$_POST['calificacion'];
        $coment = trim($_POST['comentario']);
        if ($calif >= 1 && $calif <= 5 && !empty($coment)) {
            $stmt = $pdo->prepare("INSERT INTO resenas (producto_id, usuario_id, calificacion, comentario, fecha) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$id_producto, $_SESSION['user_id'], $calif, $coment]);
            setMsg('success', '¡Gracias por tu opinión!');
        }
        redirect("producto_detalle.php?id=$id_producto");
    }
}

// --- 3. CONSULTAS ---
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? AND eliminado = 0");
$stmt->execute([$id_producto]);
$p = $stmt->fetch();
if (!$p) redirect('catalogo.php');

$soy_vendedor = is_logged_in() && ($_SESSION['user_id'] == $p['vendedor_id']);

$stmt_preg = $pdo->prepare("SELECT p.*, c.nombre FROM preguntas p JOIN clientes c ON p.usuario_id = c.id WHERE p.producto_id = ? ORDER BY p.fecha DESC");
$stmt_preg->execute([$id_producto]);
$preguntas = $stmt_preg->fetchAll();

$stmt_res = $pdo->prepare("SELECT r.*, c.nombre FROM resenas r JOIN clientes c ON r.usuario_id = c.id WHERE r.producto_id = ? ORDER BY r.fecha DESC");
$stmt_res->execute([$id_producto]);
$resenas = $stmt_res->fetchAll();

$img = $p['archivo'] ? "uploads/productos/".$p['archivo'] : "https://via.placeholder.com/500?text=Sin+Imagen";
if(!file_exists("uploads/productos/" . $p['archivo'])) $img = "https://via.placeholder.com/500?text=Sin+Imagen";

// --- 4. CARGAR VISTA ---
require_once __DIR__ . '/includes/header.php';
?>

<div class="container mb-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="catalogo.php">Catálogo</a></li>
            <li class="breadcrumb-item active"><?php echo e($p['nombre']); ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <img src="<?php echo $img; ?>" class="img-fluid w-100" style="object-fit: cover; min-height: 400px;">
            </div>
        </div>

        <div class="col-md-6">
            <h1 class="fw-bold text-dark mb-2"><?php echo e($p['nombre']); ?></h1>
            <p class="text-muted mb-4">SKU: <?php echo e($p['codigo']); ?></p>
            <h2 class="display-5 fw-bold text-primary mb-3">$<?php echo number_format($p['precio'], 2); ?></h2>
            <p class="lead text-secondary mb-4"><?php echo nl2br(e($p['descripcion'])); ?></p>

            <div class="mb-4">
                <?php if ($p['stock'] > 5): ?>
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Disponible (<?php echo $p['stock']; ?>)</span>
                <?php elseif ($p['stock'] > 0): ?>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">¡Últimas <?php echo $p['stock']; ?> unidades!</span>
                <?php else: ?>
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Agotado</span>
                <?php endif; ?>
            </div>

            <hr class="my-4">

            <?php if ($soy_vendedor): ?>
                <div class="alert alert-info border-0 rounded-4">
                    Estás viendo tu producto. <a href="vendedor/mis_productos.php" class="alert-link fw-bold">Gestionar</a>
                </div>
            <?php elseif ($p['stock'] > 0): ?>
                <form action="carrito/api_carrito.php" method="POST" class="d-flex gap-3 flex-wrap">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="producto_id" value="<?php echo $p['id']; ?>">
                    
                    <div class="input-group w-auto">
                        <span class="input-group-text bg-white border fw-bold">Cant.</span>
                        <input type="number" name="cantidad" value="1" min="1" max="<?php echo $p['stock']; ?>" class="form-control text-center" style="width: 80px;">
                    </div>

                    <button type="submit" name="redireccion" value="producto" class="btn btn-outline-primary rounded-pill px-4 fw-bold flex-grow-1">Agregar</button>
                    <button type="submit" name="redireccion" value="checkout" class="btn btn-primary rounded-pill px-4 fw-bold flex-grow-1 shadow">Comprar</button>
                </form>
            <?php else: ?>
                <div class="alert alert-danger border-0 rounded-4 w-100 text-center">
                    <i class="fas fa-times-circle me-2"></i> <strong>Producto Agotado</strong>
                </div>
                <button class="btn btn-secondary w-100 rounded-pill py-3" disabled>No Disponible</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-5 g-4">
        <div class="col-lg-6" id="seccion-opiniones">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0"><h5 class="fw-bold mb-0 text-primary">Preguntas</h5></div>
                <div class="card-body">
                    <div class="mb-4" style="max-height: 400px; overflow-y: auto;">
                        <?php if(empty($preguntas)): ?><p class="text-muted small">Sin preguntas.</p>
                        <?php else: foreach($preguntas as $preg): ?>
                            <div class="mb-3 border-bottom pb-3">
                                <p class="fw-bold mb-1 small"><?php echo e($preg['nombre']); ?></p>
                                <p class="mb-2 text-secondary small">"<?php echo e($preg['pregunta']); ?>"</p>
                                <?php if($preg['respuesta']): ?>
                                    <div class="bg-light p-2 rounded-3 ms-3 small border-start border-3 border-success">
                                        <span class="fw-bold text-success">Vendedor:</span> <?php echo e($preg['respuesta']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                    <?php if(is_logged_in() && !$soy_vendedor): ?>
                        <form method="POST">
                            <input type="hidden" name="nueva_pregunta" value="1">
                            <div class="input-group">
                                <input type="text" name="pregunta" class="form-control" placeholder="Escribe tu duda..." required>
                                <button class="btn btn-primary" type="submit">Enviar</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0"><h5 class="fw-bold mb-0 text-warning">Opiniones</h5></div>
                <div class="card-body">
                    <div class="mb-4" style="max-height: 400px; overflow-y: auto;">
                        <?php if(empty($resenas)): ?><p class="text-muted small">Sin opiniones.</p>
                        <?php else: foreach($resenas as $res): ?>
                            <div class="mb-3 border-bottom pb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold small"><?php echo e($res['nombre']); ?></span>
                                    <span class="text-warning small"><?php echo str_repeat("★", $res['calificacion']); ?></span>
                                </div>
                                <p class="mb-0 text-muted small">"<?php echo e($res['comentario']); ?>"</p>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                    <?php if(is_logged_in() && !$soy_vendedor): ?>
                        <form method="POST" class="bg-light p-3 rounded-3">
                            <input type="hidden" name="nueva_resena" value="1">
                            <select name="calificacion" class="form-select form-select-sm mb-2"><option value="5">Excelente</option><option value="4">Bueno</option><option value="3">Regular</option></select>
                            <textarea name="comentario" class="form-control form-control-sm mb-2" required placeholder="Opinión..."></textarea>
                            <button type="submit" class="btn btn-warning btn-sm w-100 text-white">Publicar</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>