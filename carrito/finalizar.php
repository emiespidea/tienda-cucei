<?php
// carrito/finalizar.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php'; // Solo DB, sin HTML header

if (session_status() === PHP_SESSION_NONE) session_start();

// Validaciones previas
if (!is_logged_in() || empty($_SESSION['carrito'])) {
    redirect('catalogo.php');
}

$cliente_id = $_SESSION['user_id'];
$carrito = $_SESSION['carrito'];
$fecha = date('Y-m-d H:i:s');

// Recibir datos form
$lugar_post = $_POST['lugar_entrega'] ?? 'Punto a convenir';
$nota_post = $_POST['nota'] ?? '';
$metodo = $_POST['metodo_pago'] ?? 'Efectivo';
$lugar_completo = $lugar_post . " - Nota: " . $nota_post;
$status = ($metodo === 'PayPal') ? 2 : 1; 

try {
    // --- INICIO DE TRANSACCIÓN ---
    $pdo->beginTransaction();

    // A. Insertar Encabezado del Pedido
    $sql = "INSERT INTO pedidos (cliente_id, fecha, status, metodo_pago, lugar_entrega) 
            VALUES (:cid, :fecha, :stat, :metodo, :lugar)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'cid' => $cliente_id,
        'fecha' => $fecha,
        'stat' => $status,
        'metodo' => $metodo,
        'lugar' => $lugar_completo
    ]);
    
    $pedido_id = $pdo->lastInsertId();

    // B. Procesar cada producto del carrito
    foreach ($carrito as $prod_id => $cantidad) {
        // Consultar producto BLOQUEANDO la fila para evitar errores de concurrencia
        $stmt_prod = $pdo->prepare("SELECT precio, vendedor_id, stock, nombre FROM productos WHERE id = :id FOR UPDATE");
        $stmt_prod->execute(['id' => $prod_id]);
        $prod_data = $stmt_prod->fetch();

        if (!$prod_data) {
            throw new Exception("El producto ID $prod_id ya no existe.");
        }

        // --- VALIDACIÓN DE STOCK CRÍTICA ---
        if ($prod_data['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente para: " . $prod_data['nombre'] . ". Disponibles: " . $prod_data['stock']);
        }

        // Insertar Detalle
        $sql_det = "INSERT INTO pedidos_productos (pedido_id, vendedor_id, producto_id, cantidad, precio) 
                    VALUES (:pid, :vid, :prid, :cant, :precio)";
        $stmt_det = $pdo->prepare($sql_det);
        $stmt_det->execute([
            'pid' => $pedido_id,
            'vid' => $prod_data['vendedor_id'],
            'prid' => $prod_id,
            'cant' => $cantidad,
            'precio' => $prod_data['precio']
        ]);

        // --- RESTAR STOCK ---
        $nuevo_stock = $prod_data['stock'] - $cantidad;
        
        // Actualizar en BD
        $stmt_upd = $pdo->prepare("UPDATE productos SET stock = :stock WHERE id = :id");
        $stmt_upd->execute(['stock' => $nuevo_stock, 'id' => $prod_id]);
    }

    // --- COMMIT (Confirmar cambios) ---
    $pdo->commit();

    // Limpiar carrito
    unset($_SESSION['carrito']);

    // Redirección
    if ($metodo === 'PayPal') {
        redirect('carrito/pasarela_paypal.php?pedido=' . $pedido_id);
    } else {
        redirect('carrito/exito.php?folio=' . $pedido_id);
    }

} catch (Exception $e) {
    // --- ROLLBACK (Deshacer todo si hubo error) ---
    $pdo->rollBack();
    setMsg('danger', 'Error al procesar pedido: ' . $e->getMessage());
    redirect('carrito/ver_carrito.php');
}
?>