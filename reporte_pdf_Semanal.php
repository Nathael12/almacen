<?php
require('fpdf186/fpdf.php');
include("common/conexion.php");

// OBTENER Y VALIDAR RANGO DE FECHAS


// Si se recibe la fecha por GET la usamos; de lo contrario, la fecha actual
$fecha_referencia = isset($_GET['fecha']) && !empty($_GET['fecha'])
    ? $_GET['fecha']
    : date('Y-m-d');

$timestamp = strtotime($fecha_referencia);
$dia_semana = date('N', $timestamp); // 1 = Lunes, 7 = Domingo

// Calcular el lunes y el domingo correspondientes a la semana
$lunes = date('Y-m-d', strtotime("-" . ($dia_semana - 1) . " days", $timestamp));
$domingo = date('Y-m-d', strtotime("+6 days", strtotime($lunes)));

// CONFIGURACIÓN DEL PDF


$pdf = new FPDF();
$pdf->AddPage();

// LOGO
if (file_exists('imagenes/inpi.png')) {
    $pdf->Image('imagenes/inpi.png', 10, 8, 45);
}

// ENCABEZADO INSTITUCIONAL
$pdf->Ln(25);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 6, utf8_decode('INSTITUTO NACIONAL DE LOS PUEBLOS INDÍGENAS'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'CASA COMUNITARIA DEL ESTUDIANTE INDIGENA', 0, 1, 'C');
$pdf->Cell(0, 5, '"JACINTO PAAT"', 0, 1, 'C');

$pdf->Ln(3);

// TÍTULO Y PERIODO DEL REPORTE
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, 'REQUERIMIENTO SEMANAL DE ALMACEN', 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(
    0,
    6,
    utf8_decode('Semana del ' . date('d/m/Y', strtotime($lunes)) . ' al ' . date('d/m/Y', strtotime($domingo))),
    0,
    1,
    'C'
);

$pdf->Ln(5);

// ENCABEZADOS DE LA TABLA
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 8, 'FECHA', 1, 0, 'C');
$pdf->Cell(25, 8, 'DIA', 1, 0, 'C');
$pdf->Cell(70, 8, 'REQUERIMIENTO', 1, 0, 'C');
$pdf->Cell(20, 8, 'CANT.', 1, 0, 'C');
$pdf->Cell(25, 8, 'SOLICITO', 1, 0, 'C');
$pdf->Cell(25, 8, 'ENTREGO', 1, 1, 'C');

$pdf->SetFont('Arial', '', 9);


//  CONSULTA A LA BASE DE DATOS


// Rango completo desde el primer segundo del Lunes hasta el último del Domingo
$sql = "
SELECT 
    s.fecha_salida,
    p.nombre_comercial,
    s.cantidad_usada
FROM salidas s
INNER JOIN productos p 
    ON s.producto_id = p.id_producto
WHERE s.fecha_salida >= '$lunes 00:00:00' 
  AND s.fecha_salida <= '$domingo 23:59:59'
ORDER BY s.fecha_salida ASC, s.id_salida ASC
";

$result = $conn->query($sql);

// Nombres de días mapeados por número de día de la semana (1 = Lunes ... 7 = Domingo)
$dias_semana_es = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    7 => 'Domingo'
];

$total_articulos_semana = 0;

//  IMPRESIÓN DE REGISTROS


if (!$result || $result->num_rows == 0) {

    $pdf->Cell(
        190,
        10,
        utf8_decode('No se registraron productos utilizados durante esta semana.'),
        1,
        1,
        'C'
    );

} else {

    while ($row = $result->fetch_assoc()) {

        $time_salida = strtotime($row['fecha_salida']);
        $fecha_formateada = date('d/m/Y', $time_salida);

        // Obtener el día por su índice numérico (1-7)
        $num_dia = date('N', $time_salida);
        $nombre_dia = $dias_semana_es[$num_dia] ?? 'S/D';

        $cantidad = (int)$row['cantidad_usada'];
        $total_articulos_semana += $cantidad;

        $pdf->Cell(25, 8, $fecha_formateada, 1, 0, 'C');
        $pdf->Cell(25, 8, utf8_decode($nombre_dia), 1, 0, 'C');
        $pdf->Cell(70, 8, utf8_decode($row['nombre_comercial']), 1, 0, 'L');
        $pdf->Cell(20, 8, $cantidad, 1, 0, 'C');
        $pdf->Cell(25, 8, '', 1, 0, 'C');
        $pdf->Cell(25, 8, '', 1, 1, 'C');
    }

    // FILA DE TOTALES DE LA SEMANA
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(120, 8, utf8_decode('TOTAL DE ARTÍCULOS CONSUMIDOS EN LA SEMANA'), 1, 0, 'R');
    $pdf->Cell(20, 8, $total_articulos_semana, 1, 0, 'C');
    $pdf->Cell(50, 8, '', 1, 1, 'C');
}

//  PIE DEL REPORTE


$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, utf8_decode('PERÍODO DE CONSULTA'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(
    0,
    5,
    date('d/m/Y', strtotime($lunes)) . ' - ' . date('d/m/Y', strtotime($domingo)),
    0,
    1,
    'C'
);

// SALIDA DEL DOCUMENTO
$pdf->Output();
?>