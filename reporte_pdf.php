<?php
require('fpdf186/fpdf.php');
include("common/conexion.php");

$pdf = new FPDF();
$pdf->AddPage();

// LOGO
if (file_exists('imagenes/inpi.png')) {
    $pdf->Image('imagenes/inpi.png', 10, 8, 45);
}

// ENCABEZADO
$pdf->Ln(25);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 6, 'INSTITUTO NACIONAL DE LOS PUEBLOS INDIGENAS', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'CASA COMUNITARIA DEL ESTUDIANTE INDIGENA', 0, 1, 'C');
$pdf->Cell(0, 5, '"JACINTO PAAT"', 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, 'REQUERIMIENTO DIARIO', 0, 1, 'C');
$pdf->Ln(5);

// TABLA ENCABEZADO
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 8, 'FECHA', 1, 0, 'C');
$pdf->Cell(25, 8, 'DIA', 1, 0, 'C');
$pdf->Cell(70, 8, 'REQUERIMIENTO', 1, 0, 'C');
$pdf->Cell(20, 8, 'CANT.', 1, 0, 'C');
$pdf->Cell(25, 8, 'SOLICITO', 1, 0, 'C');
$pdf->Cell(25, 8, 'ENTREGO', 1, 1, 'C');

$pdf->SetFont('Arial', '', 9);

// NUEVA CONSULTA: Lee directamente del historial de salidas del día de hoy
$sql = "
SELECT s.fecha_salida, p.nombre_comercial, s.cantidad_usada
FROM salidas s
INNER JOIN productos p ON s.producto_id = p.id_producto
WHERE s.fecha_salida = CURDATE()
ORDER BY s.id_salida DESC
";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $pdf->Cell(190, 10, utf8_decode('No hay productos utilizados el día de hoy'), 1, 1, 'C');
} else {
    $dias = [
        'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miercoles',
        'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sabado', 'Sunday' => 'Domingo'
    ];

    while ($row = $result->fetch_assoc()) {
        $fecha = date('d/m/Y', strtotime($row['fecha_salida']));
        $dia_en = date('l', strtotime($row['fecha_salida']));
        $dia = $dias[$dia_en] ?? 'S/D';

        $pdf->Cell(25, 8, $fecha, 1, 0, 'C');
        $pdf->Cell(25, 8, utf8_decode($dia), 1, 0, 'C');
        $pdf->Cell(70, 8, utf8_decode($row['nombre_comercial']), 1, 0, 'L');
        
        // Muestra la cantidad exacta guardada en el registro de salida
        $pdf->Cell(20, 8, $row['cantidad_usada'], 1, 0, 'C'); 
        
        $pdf->Cell(25, 8, '', 1, 0, 'C'); 
        $pdf->Cell(25, 8, '', 1, 1, 'C'); 
    }
}

$pdf->Output();
?>