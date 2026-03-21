<?php

include("../common/conexion.php");

$id = $_GET['id'];

$nombre_comercial = $_POST['nombre_comercial'];
$nombre_comun = $_POST['nombre_comun'];
$categoria_producto = $_POST['categoria_producto'];

$unidad_id = isset($_POST['unidad_id']) ? $_POST['unidad_id'] : null;

$sql = "UPDATE productos SET
nombre_comercial='$nombre_comercial',
nombre_comun='$nombre_comun',
categoria_producto='$categoria_producto',
unidad_id='$unidad_id'
WHERE id_producto=$id";

$conn->query($sql);

header("Location: ../productos.php");

?>