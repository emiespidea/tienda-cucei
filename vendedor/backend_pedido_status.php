<?php
session_start();
include '../config/conecta.php';

// Seguridad
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 'vendedor') { 
    header("Location: ../index.php"); 
    exit(); 
}

$pedido_id = $_POST['pedido_id'];
$status_nuevo = $_POST['status_nuevo']; // Generalmente es 3

// Actualizar en base de datos
$sql = "UPDATE pedidos SET status = '$status_nuevo' WHERE id = '$pedido_id'";

if(mysqli_query($con, $sql)){
    echo "<script>alert('Estado actualizado correctamente.'); window.location='pedidos.php';</script>";
} else {
    echo "Error SQL: " . mysqli_error($con);
}
?>