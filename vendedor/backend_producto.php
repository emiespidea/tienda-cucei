<?php
session_start();
include '../config/conecta.php';

// Validar que sea vendedor
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 'vendedor') { die("Acceso denegado"); }

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';
$id_vendedor = $_SESSION['id'];

// --- INSERTAR ---
if ($accion == 'insertar') {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);
    $desc = mysqli_real_escape_string($con, $_POST['descripcion']);
    $precio = $_POST['precio'];
    $costo = $_POST['costo'];
    $stock = $_POST['stock'];

    $nombre_archivo = "";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = "prod_" . time() . "." . $ext;
        $carpeta_destino = "../uploads/productos/";
        if (!file_exists($carpeta_destino)) { mkdir($carpeta_destino, 0777, true); }
        move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta_destino . $nombre_archivo);
    }

    $sql = "INSERT INTO productos (vendedor_id, nombre, descripcion, codigo, archivo, precio, costo, stock) 
            VALUES ('$id_vendedor', '$nombre', '$desc', '$codigo', '$nombre_archivo', '$precio', '$costo', '$stock')";

    if (mysqli_query($con, $sql)) {
        // CAMBIO CLAVE: Redirección inmediata sin pantalla blanca
        header("Location: mis_productos.php?msg=publicado");
        exit();
    } else {
        header("Location: publicar.php?msg=error");
        exit();
    }
}

// --- ELIMINAR ---
if ($accion == 'eliminar') {
    $id_producto = $_POST['id'];
    $sql = "UPDATE productos SET eliminado = 1 WHERE id = $id_producto AND vendedor_id = $id_vendedor";
    
    if (mysqli_query($con, $sql)) {
        header("Location: mis_productos.php?msg=eliminado");
        exit();
    }
}
?>