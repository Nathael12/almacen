<?php
include("../common/conexion.php");

if (isset($_GET['id_producto'])) {
    $id_producto = intval($_GET['id_producto']);

    $sql = "SELECT id_lote, cantidad, fecha_caducidad 
            FROM lotes 
            WHERE producto_id = ? AND estado = 1 AND cantidad > 0 
            ORDER BY fecha_caducidad ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();

    $lotes = [];
    while ($row = $result->fetch_assoc()) {
        // Formateamos la fecha a algo legible
        $row['fecha_f'] = date('d/m/Y', strtotime($row['fecha_caducidad']));
        $lotes[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($lotes);
    exit;
}