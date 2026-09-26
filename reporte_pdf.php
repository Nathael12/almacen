<?php

require('fpdf186/fpdf.php');
include('common/conexion.php');

$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('n');
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');

if ($mes < 1 || $mes > 12) {
    $mes = date('n');
}

if ($anio < 2000 || $anio > 2100) {
    $anio = date('Y');
}

$primer_dia = sprintf('%04d-%02d-01', $anio, $mes);
$ultimo_dia = date('Y-m-t', strtotime($primer_dia));

$meses = [
    1 => 'ENERO',
    2 => 'FEBRERO',
    3 => 'MARZO',
    4 => 'ABRIL',
    5 => 'MAYO',
    6 => 'JUNIO',
    7 => 'JULIO',
    8 => 'AGOSTO',
    9 => 'SEPTIEMBRE',
    10 => 'OCTUBRE',
    11 => 'NOVIEMBRE',
    12 => 'DICIEMBRE'
];

$nombre_mes = $meses[$mes];

$sql = "
    SELECT
        p.nombre_comercial,
        SUM(l.cantidad) AS cantidad
    FROM lotes l
    INNER JOIN productos p
        ON l.producto_id = p.id_producto
    WHERE l.fecha_entrada BETWEEN '$primer_dia' AND '$ultimo_dia'
    GROUP BY p.id_producto, p.nombre_comercial
    ORDER BY p.nombre_comercial ASC
";

$resultado = $conn->query($sql);

if (!$resultado) {
    die("Error en la consulta: " . $conn->error);
}

$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(
    0,
    10,
    iconv('UTF-8', 'windows-1252', 'REPORTE DE PRODUCTOS INGRESADOS'),
    0,
    1,
    'C'
);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(
    0,
    8,
    $nombre_mes . ' ' . $anio,
    0,
    1,
    'C'
);

$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 11);

$pdf->Cell(130, 8, 'PRODUCTO', 1, 0, 'C');
$pdf->Cell(50, 8, 'CANTIDAD', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);

$total = 0;

if ($resultado->num_rows > 0) {

    while ($fila = $resultado->fetch_assoc()) {

        $producto = iconv(
            'UTF-8',
            'windows-1252',
            $fila['nombre_comercial']
        );

        $cantidad = intval($fila['cantidad']);

        $pdf->Cell(130, 8, $producto, 1, 0, 'L');
        $pdf->Cell(50, 8, $cantidad, 1, 1, 'C');

        $total += $cantidad;
    }

} else {

    $pdf->Cell(
        180,
        8,
        'No se encontraron productos ingresados en este mes.',
        1,
        1,
        'C'
    );
}

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(130, 8, 'TOTAL', 1, 0, 'R');
$pdf->Cell(50, 8, $total, 1, 1, 'C');

$pdf->Ln(10);

$pdf->SetFont('Arial', '', 9);

$pdf->Cell(
    0,
    6,
    'Fecha de generacion: ' . date('d/m/Y'),
    0,
    1,
    'R'
);

$pdf->Output(
    'I',
    'reporte_productos_' . $mes . '_' . $anio . '.pdf'
);

?>