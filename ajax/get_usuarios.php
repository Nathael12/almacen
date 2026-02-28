<?php
include("../common/conexion.php");

$datos = [];
$sql = "SELECT * FROM usuarios WHERE estado = 1;";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()){
    $datos[] = $row;
}

echo json_encode($datos);