<?php
// carrito/pasarela_paypal.php
require_once __DIR__ . '/../includes/functions.php'; // Solo funciones, sin header completo
$pedido_id = $_GET['pedido'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procesando Pago - PayPal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; }
    </style>
</head>
<body class="text-center">

    <div class="card border-0 shadow-lg rounded-4 p-5" style="max-width: 450px;">
        <div class="mb-4">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" height="40">
        </div>
        
        <div class="spinner-border text-primary mb-4" style="width: 3rem; height: 3rem;" role="status"></div>
        
        <h4 class="fw-bold text-dark">Conectando con el banco...</h4>
        <p class="text-muted small">Por favor no cierres esta ventana. Estamos validando tu pago seguro.</p>
    </div>

    <script>
        // Simular espera de 2.5 segundos y redirigir al éxito
        setTimeout(function() {
            window.location.href = "exito.php?folio=<?php echo $pedido_id; ?>"; 
        }, 2500);
    </script>
</body>
</html>
