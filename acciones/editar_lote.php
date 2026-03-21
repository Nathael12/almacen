<?php

include("../common/conexion.php");

$id = $_GET['id'];

$producto_id = $_POST['producto_id'];
$proveedor_id = $_POST['proveedor_id'];
$fecha_entrada = $_POST['fecha_entrada'];
$fecha_caducidad = $_POST['fecha_caducidad'];

$sql = "UPDATE lotes SET
        producto_id='$producto_id',
        proveedor_id='$proveedor_id',
        fecha_entrada='$fecha_entrada',
        fecha_caducidad='$fecha_caducidad'
        WHERE id_lote='$id'";

$conn->query($sql);

header("Location: ../lotes.php");

?>