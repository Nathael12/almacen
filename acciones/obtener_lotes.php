<?php

include("../common/conexion.php");

header('Content-Type: application/json; charset=utf-8');

$id_producto = isset($_GET['id_producto'])
    ? intval($_GET['id_producto'])
    : 0;

if ($id_producto <= 0) {

    echo json_encode([]);
    exit;
}

$sql = "
    SELECT
        id_lote,

        DATE_FORMAT(
            fecha_entrada,
            '%d/%m/%Y'
        ) AS fecha_entrada_f,

        DATE_FORMAT(
            fecha_caducidad,
            '%d/%m/%Y'
        ) AS fecha_caducidad_f,

        cantidad

    FROM lotes

    WHERE producto_id = ?
    AND estado = 1
    AND cantidad > 0

    ORDER BY fecha_caducidad ASC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        'error' => $conn->error
    ]);

    exit;
}

$stmt->bind_param(
    "i",
    $id_producto
);

$stmt->execute();

$result = $stmt->get_result();

$lotes = [];

while ($row = $result->fetch_assoc()) {

    $lotes[] = [

        'id_lote' => intval($row['id_lote']),

        'fecha_entrada_f' => $row['fecha_entrada_f'],

        'fecha_caducidad_f' => $row['fecha_caducidad_f'],

        'cantidad' => intval($row['cantidad'])

    ];

}

echo json_encode($lotes);

$stmt->close();

$conn->close();

?>