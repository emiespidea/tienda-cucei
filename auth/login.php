<?php
// auth/login.php
require_once __DIR__ . '/../includes/header.php';

// Si ya está logueado, redirigir al inicio o panel
if (is_logged_in()) {
    redirect('index.php');
}

// PROCESAR FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($correo) || empty($password)) {
        setMsg('danger', 'Por favor ingresa correo y contraseña.');
    } else {
        // 1. Buscar usuario por correo (PDO Seguro)
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE correo = :correo AND eliminado = 0 LIMIT 1");
        $stmt->execute(['correo' => $correo]);
        $user = $stmt->fetch();

        $login_exitoso = false;

        if ($user) {
            // A. Verificar contraseña con Hash Seguro (Lo ideal)
            if (password_verify($password, $user['password'])) {
                $login_exitoso = true;
            } 
            // B. COMPATIBILIDAD: Verificar MD5 antiguo y actualizarlo
            elseif ($user['password'] === md5($password)) {
                $login_exitoso = true;
                // Actualizar hash en BD silenciosamente
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE clientes SET password = :p WHERE id = :id");
                $upd->execute(['p' => $newHash, 'id' => $user['id']]);
            }
        }

        if ($login_exitoso) {
            // 2. Crear variables de sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            $_SESSION['user_rol'] = $user['rol'];

            setMsg('success', '¡Bienvenido de nuevo, ' . e($user['nombre']) . '!');
            
            // 3. Redirección inteligente por rol
            if ($user['rol'] === 'vendedor') {
                redirect('vendedor/dashboard.php');
            } elseif ($user['rol'] === 'admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            setMsg('danger', 'Correo o contraseña incorrectos.');
        }
    }
}
?>

<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5 col-lg-4">
        
        <div class="card shadow-lg border-0 rounded-4 animate__animated animate__fadeInUp">
            <div class="card-body p-5">
                
                <div class="text-center mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-primary" style="width: 70px; height: 70px;">
                        <i class="fas fa-user-circle fa-3x"></i>
                    </div>
                    <h3 class="fw-bold text-dark">Iniciar Sesión</h3>
                    <p class="text-muted small">Ingresa a tu cuenta institucional</p>
                </div>

                <form method="POST" action="login.php">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control rounded-3" id="correo" name="correo" placeholder="name@alumnos.udg.mx" required value="<?php echo e($_POST['correo'] ?? ''); ?>">
                        <label for="correo"><i class="fas fa-envelope me-2 text-muted"></i>Correo Institucional</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Contraseña" required>
                        <label for="password"><i class="fas fa-lock me-2 text-muted"></i>Contraseña</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                            Entrar <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted mb-0">¿No tienes cuenta?</p>
                    <a href="registro.php" class="fw-bold text-primary text-decoration-none">Regístrate aquí</a>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>