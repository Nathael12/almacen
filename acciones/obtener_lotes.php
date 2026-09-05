<?php
include("../common/conexion.php");

header('Content-Type: application/json');

$id_producto = $_GET['id_producto'] ?? 0;

$sql = "SELECT id_lote, 
               DATE_FORMAT(fecha_entrada, '%d/%m/%Y') as fecha_entrada_f, 
               DATE_FORMAT(fecha_caducidad, '%d/%m/%Y') as fecha_caducidad_f 
        FROM lotes 
        WHERE producto_id = ? AND estado = 1 
        ORDER BY fecha_caducidad ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();

$lotes = [];
while ($row = $result->fetch_assoc()) {
    $lotes[] = $row;
}

echo json_encode($lotes);