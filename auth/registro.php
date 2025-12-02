<?php
// auth/registro.php

// 1. CARGA DE DEPENDENCIAS (Antes de cualquier HTML)
// Cargamos la BD y funciones manualmente aquí para procesar la lógica primero
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Verificar si ya hay sesión
if (is_logged_in()) {
    redirect('index.php');
}

// 2. PROCESAR REGISTRO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $pass1 = $_POST['password'] ?? '';
    $pass2 = $_POST['confirm_password'] ?? '';
    $rol = $_POST['rol'] ?? 'comprador';

    $errores = [];

    // Validaciones
    if (strpos($correo, '@alumnos.udg.mx') === false) {
        $errores[] = "Debes usar un correo institucional (@alumnos.udg.mx).";
    }
    if ($pass1 !== $pass2) {
        $errores[] = "Las contraseñas no coinciden.";
    }
    if (strlen($pass1) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    // Verificar duplicados
    try {
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE correo = :c");
        $stmt->execute(['c' => $correo]);
        if ($stmt->rowCount() > 0) {
            $errores[] = "Este correo ya está registrado.";
        }
    } catch (PDOException $e) {
        $errores[] = "Error de conexión: " . $e->getMessage();
    }

    // Si no hay errores, insertar
    if (empty($errores)) {
        // Encriptar contraseña
        $hash = password_hash($pass1, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO clientes (nombre, apellidos, correo, password, rol) VALUES (:nom, :ape, :cor, :pass, :rol)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nom' => $nombre,
                'ape' => $apellidos,
                'cor' => $correo,
                'pass' => $hash,
                'rol' => $rol
            ]);

            setMsg('success', 'Cuenta creada con éxito. ¡Inicia sesión!');
            
            // REDIRECCIÓN: Ahora funcionará porque no hemos impreso HTML aún
            redirect('auth/login.php');

        } catch (PDOException $e) {
            setMsg('danger', 'Error en base de datos: ' . $e->getMessage());
        }
    } else {
        // Cargar errores en el sistema de mensajes para mostrarlos abajo
        foreach ($errores as $error) {
            setMsg('danger', $error);
        }
    }
}

// 3. CARGAR VISTA (Ahora sí cargamos el HTML)
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center my-4">
    <div class="col-md-8 col-lg-6">
        
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-5">
                
                <h2 class="fw-bold text-center text-primary mb-4">Crear Cuenta Nueva</h2>
                
                <form method="POST" action="registro.php" class="row g-3">
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Nombre</label>
                        <input type="text" name="nombre" class="form-control rounded-3" required value="<?php echo e($_POST['nombre'] ?? ''); ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Apellidos</label>
                        <input type="text" name="apellidos" class="form-control rounded-3" required value="<?php echo e($_POST['apellidos'] ?? ''); ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Correo Institucional</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-graduation-cap"></i></span>
                            <input type="email" name="correo" class="form-control border-start-0 ps-0" placeholder="codigo@alumnos.udg.mx" required value="<?php echo e($_POST['correo'] ?? ''); ?>">
                        </div>
                        <div class="form-text text-primary small"><i class="fas fa-info-circle me-1"></i>Solo correos @alumnos.udg.mx</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Contraseña</label>
                        <input type="password" name="password" class="form-control rounded-3" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Confirmar Contraseña</label>
                        <input type="password" name="confirm_password" class="form-control rounded-3" required>
                    </div>

                    <div class="col-12 mt-3">
                        <label class="form-label small fw-bold text-muted">Quiero ser:</label>
                        <div class="d-flex gap-3">
                            <div class="form-check border p-3 rounded-3 flex-fill bg-white">
                                <input class="form-check-input" type="radio" name="rol" id="rol1" value="comprador" checked style="transform: scale(1.2); cursor: pointer;">
                                <label class="form-check-label w-100 fw-bold ms-2" for="rol1" style="cursor: pointer;">
                                    🛒 Comprador
                                </label>
                            </div>
                            <div class="form-check border p-3 rounded-3 flex-fill bg-light">
                                <input class="form-check-input" type="radio" name="rol" id="rol2" value="vendedor" style="transform: scale(1.2); cursor: pointer;">
                                <label class="form-check-label w-100 fw-bold ms-2 text-primary" for="rol2" style="cursor: pointer;">
                                    💼 Vendedor
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow">
                            Registrarme
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4 border-top pt-3">
                    <small class="text-muted">¿Ya tienes cuenta?</small>
                    <a href="login.php" class="text-decoration-none fw-bold ms-1">Inicia Sesión</a>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>