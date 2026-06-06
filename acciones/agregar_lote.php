<?php
include("../common/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $producto_id = $_POST['producto_id'];
    $proveedor_id = $_POST['proveedor_id'];
    $cantidad = $_POST['cantidad'];
    $fecha_entrada = $_POST['fecha_entrada'];
    $fecha_caducidad = $_POST['fecha_caducidad'];

    $sql = "INSERT INTO lotes
            (producto_id, proveedor_id, cantidad, fecha_entrada, fecha_caducidad, estado)
            VALUES (?, ?, ?, ?, ?, 1)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iiiss",
        $producto_id,
        $proveedor_id,
        $cantidad,
        $fecha_entrada,
        $fecha_caducidad
    );

    if ($stmt->execute()) {
        // Redirección limpia: intenta volver un nivel atrás. 
        // Si tu entorno usa rutas virtuales absolutas, puedes cambiarlo por "/almacen/lotes.php"
        header("Location: ../lotes.php");
        exit;
    } else {
        echo "Error al insertar en la base de datos: " . $conn->error;
    }
}
?>