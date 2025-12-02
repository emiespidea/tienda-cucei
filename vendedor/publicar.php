<?php
// vendedor/publicar.php

// 1. CARGA DE DEPENDENCIAS (Sin HTML todavía)
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// 2. SEGURIDAD: Validar que sea vendedor
if (!is_logged_in() || $_SESSION['user_rol'] !== 'vendedor') {
    redirect('index.php');
}

// 3. PROCESAR FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $codigo = trim($_POST['codigo']);
    $desc = trim($_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    $costo = (float)$_POST['costo'];
    $stock = (int)$_POST['stock'];
    
    // Manejo de Imagen
    $nombre_archivo = null;
    $error_img = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($ext, $permitidos)) {
            // Nombre único para evitar que se sobrescriban
            $nombre_archivo = "prod_" . time() . "_" . uniqid() . "." . $ext;
            $destino = __DIR__ . '/../uploads/productos/';
            
            // Crear carpeta si no existe
            if (!is_dir($destino)) {
                mkdir($destino, 0777, true);
            }
            
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destino . $nombre_archivo)) {
                $error_img = "Error al mover la imagen al servidor.";
            }
        } else {
            $error_img = "Formato no válido. Solo JPG, PNG o WEBP.";
        }
    } else {
        $error_img = "Debes subir una imagen del producto.";
    }

    if (!$error_img) {
        try {
            $sql = "INSERT INTO productos (vendedor_id, nombre, descripcion, codigo, archivo, precio, costo, stock) 
                    VALUES (:vid, :nom, :desc, :cod, :arch, :prec, :cost, :stk)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'vid' => $_SESSION['user_id'],
                'nom' => $nombre,
                'desc' => $desc,
                'cod' => $codigo,
                'arch' => $nombre_archivo,
                'prec' => $precio,
                'cost' => $costo,
                'stk' => $stock
            ]);
            
            setMsg('success', 'Producto publicado correctamente.');
            // AQUÍ OCURRÍA EL ERROR ANTES: Ahora funcionará porque no hay HTML impreso
            redirect('vendedor/mis_productos.php');

        } catch (PDOException $e) {
            setMsg('danger', 'Error en Base de Datos: ' . $e->getMessage());
        }
    } else {
        setMsg('danger', $error_img);
    }
}

// 4. AHORA SÍ: CARGAR LA VISTA (HEADER HTML)
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow rounded-4 border-0 mb-5">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-primary">Publicar Nuevo Producto</h4>
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill">Cancelar</a>
                </div>
            </div>
            <div class="card-body p-4">
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control rounded-3" required placeholder="Ej: Calculadora Científica">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">SKU (Código Único)</label>
                            <input type="text" name="codigo" class="form-control rounded-3" required placeholder="Ej: CALC-001">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Descripción Detallada</label>
                            <textarea name="descripcion" class="form-control rounded-3" rows="4" placeholder="Describe el estado, marca y detalles..."></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Precio Venta ($)</label>
                            <input type="number" step="0.01" name="precio" class="form-control rounded-3" required placeholder="0.00">
                            <div class="form-text">Lo que paga el cliente</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Costo ($)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="costo" class="form-control rounded-3" required placeholder="0.00">
                                <span class="input-group-text bg-white border-0 text-muted"><i class="fas fa-eye-slash"></i></span>
                            </div>
                            <div class="form-text">Privado (Tu inversión)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Stock Inicial</label>
                            <input type="number" name="stock" class="form-control rounded-3" required value="1">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Imagen Principal</label>
                            <input type="file" name="imagen" class="form-control rounded-3" accept="image/*" required>
                            <div class="form-text">Formatos: JPG, PNG, WEBP.</div>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow fw-bold">
                                <i class="fas fa-check me-2"></i> Publicar
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>