<?php

include("../common/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $producto_id = $_POST['producto_id'];
    $proveedor_id = $_POST['proveedor_id'];
    $fecha_entrada = $_POST['fecha_entrada'];
    $fecha_caducidad = $_POST['fecha_caducidad'];

    $sql = "INSERT INTO lotes 
            (producto_id, proveedor_id, fecha_entrada, fecha_caducidad, estado)
            VALUES (?, ?, ?, ?, 1)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iiss",
        $producto_id,
        $proveedor_id,
        $fecha_entrada,
        $fecha_caducidad
    );

    if ($stmt->execute()) {

        header("Location: ../lotes.php");
        exit;

    } else {

        echo "Error: " . $conn->error;

    }

}
?>