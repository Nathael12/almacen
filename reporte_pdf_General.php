<?php
// Forzar la zona horaria del servidor PHP para que coincida con tus pruebas
date_default_timezone_set('America/Mexico_City');

require('fpdf186/fpdf.php');
include("common/conexion.php");

// Sincronizar el huso horario de la conexión MySQL
$conn->query("SET time_zone = '-06:00';");

/* =========================
    MES Y AÑO AUTOMÁTICOS
========================= */
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');

/* =========================
    PERIODO (Límites de días del mes)
========================= */
$inicioPeriodo = "01";
$finPeriodo = date("t", strtotime("$anio-$mes-01"));

$mesNombre = [
    1=>'ENERO', 2=>'FEBRERO', 3=>'MARZO', 4=>'ABRIL',
    5=>'MAYO', 6=>'JUNIO', 7=>'JULIO', 8=>'AGOSTO',
    9=>'SEPTIEMBRE', 10=>'OCTUBRE', 11=>'NOVIEMBRE', 12=>'DICIEMBRE'
];

/* =========================
    CONSULTA: CUENTA LAS FRECUENCIAS DE USO DIARIAS
========================= */
$sql = "
SELECT 
    p.id_producto,
    p.nombre_comercial,
    s.fecha_salida,
    COUNT(s.id_salida) as veces_usado
FROM salidas s
INNER JOIN productos p ON p.id_producto = s.producto_id
WHERE MONTH(s.fecha_salida) = $mes
AND YEAR(s.fecha_salida) = $anio
GROUP BY p.id_producto, s.fecha_salida
ORDER BY p.nombre_comercial, s.fecha_salida
";

$result = $conn->query($sql);

/* =========================
    MATRIZ DE SEGUIMIENTO
========================= */
$reporte = [];

while($row = $result->fetch_assoc()){
    $producto = $row['id_producto'];
    $nombre = $row['nombre_comercial'];
    $fechaStr = $row['fecha_salida'];
    $frecuencia_dia = (int)$row['veces_usado']; 
    
    $timestamp = strtotime($fechaStr);
    $diaSemana = (int)date("N", $timestamp); // Lunes (1) a Domingo (7)

    // Filtrar estrictamente de Lunes a Viernes (1 a 5) según el formato
    if($diaSemana <= 5){
        $diaMes = (int)date("d", $timestamp);
        
        // Mapeo de bloques de semanas del mes (1 a 5)
        $semana = ceil($diaMes / 7);
        if($semana > 5) $semana = 5;

        $reporte[$producto]['nombre'] = $nombre;

        if(!isset($reporte[$producto]['datos'][$semana][$diaSemana])){
            $reporte[$producto]['datos'][$semana][$diaSemana] = 0;
        }
        // Sumar las repeticiones de uso del producto en el mismo día
        $reporte[$producto]['datos'][$semana][$diaSemana] += $frecuencia_dia;
    }
}

/* =========================
    CONFIGURACIÓN DEL DOCUMENTO PDF
========================= */
$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 15);

/* LOGO INSTITUTIONAL */
if(file_exists('imagenes/inpi.png')) {
    $pdf->Image('imagenes/inpi.png', 15, 10, 38);
}

/* ENCABEZADOS DEL ANEXO OFICIAL */
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,4,utf8_decode('Programa de Apoyo a la Educación Indígena'),0,1,'C');
$pdf->Cell(0,4,utf8_decode('Casa o Comedor Comunitario del Estudiante Indígena'),0,1,'C');
$pdf->Ln(2);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,5,utf8_decode('Anexo A2.1 Registro Diario de Salidas de Almacén'),0,1,'C');
$pdf->Ln(4);

// Bloque de datos de control
$pdf->SetFont('Arial','',8);
$pdf->Cell(25, 4, 'CCPI: ', 0, 0, 'L');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(95, 4, 'HOPELCHEN.', 'B', 0, 'L'); 
$pdf->SetFont('Arial','',8);
$pdf->Cell(20, 4, 'CLAVE: ', 0, 0, 'R');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(0, 4, '040060001A03', 'B', 1, 'L');

$pdf->SetFont('Arial','',8);
$pdf->Cell(25, 5, 'MUNICIPIO: ', 0, 0, 'L');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(95, 5, 'HOPELCHEN.', 'B', 0, 'L');
$pdf->SetFont('Arial','',8);
$pdf->Cell(20, 5, 'LOCALIDAD: ', 0, 0, 'R');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(0, 5, 'HOPELCHEN.', 'B', 1, 'L');

$pdf->SetFont('Arial','',8);
$pdf->Cell(55, 5, 'NOMBRE DE LA CASA O COMEDOR: ', 0, 0, 'L');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(0, 5, utf8_decode('CASA COMUNITARIA DEL ESTUDIANTE INDIGENA "JACINTO PAAT"'), 'B', 1, 'L');

$textoPeriodo = "Registro de salidas del almacén del periodo: del  ".$inicioPeriodo."  de  ".$mesNombre[$mes]."  al  ".$finPeriodo."  de  ".$mesNombre[$mes]."  de  ".$anio.".";
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,6,utf8_decode($textoPeriodo),0,1,'L');
$pdf->Ln(3);

/* =========================
    MEDIDAS DE TABLA (Total: 277mm)
========================= */
$anchoProducto = 80; 
$anchoDia = 7;       
$anchoTotal = 22;

$pdf->SetFont('Arial','B',8);
$xInicioTabla = $pdf->GetX();
$yInicioTabla = $pdf->GetY();

// Encabezados principales
$pdf->Cell($anchoProducto, 12, utf8_decode('Descripción del artículo (nombre, marca, cantidad)'), 1, 0, 'C');

for($i=1; $i<=5; $i++){
    $pdf->Cell(5 * $anchoDia, 6, utf8_decode($i."° semana"), 1, 0, 'C');
}
$pdf->Cell($anchoTotal, 12, 'Total', 1, 0, 'C');

// Subencabezado de días laborables
$pdf->SetXY($xInicioTabla + $anchoProducto, $yInicioTabla + 6);
$pdf->SetFont('Arial','B',5.5);
$diasLetras = ['Lunes','Martes','Miérc.','Jueves','Viernes'];

for($s=1; $s<=5; $s++){
    foreach($diasLetras as $d){
        $pdf->Cell($anchoDia, 6, utf8_decode($d), 1, 0, 'C');
    }
}

$pdf->SetY($yInicioTabla + 12);

/* =========================
    CONSTRUCCIÓN DEL CUERPO
========================= */
$pdf->SetFont('Arial','',7);
$totalesColumnas = array_fill(1, 25, 0);
$totalGeneral = 0;

if(count($reporte) === 0){
    $pdf->Cell(277, 10, utf8_decode('No se encontraron registros de salidas en el mes seleccionado.'), 1, 1, 'C');
} else {
    foreach($reporte as $producto){
        $yInicioRow = $pdf->GetY();
        $xInicioRow = $pdf->GetX();

        $pdf->MultiCell($anchoProducto, 5, utf8_decode($producto['nombre']), 1, 'L');
        $yFinalRow = $pdf->GetY();
        $altoFila = $yFinalRow - $yInicioRow;

        $pdf->SetXY($xInicioRow + $anchoProducto, $yInicioRow);
        $totalProducto = 0;

        for($semana=1; $semana<=5; $semana++){
            for($dia=1; $dia<=5; $dia++){
                $valor = $producto['datos'][$semana][$dia] ?? 0;
                $totalProducto += $valor;

                $colIndex = (($semana - 1) * 5) + $dia;
                $totalesColumnas[$colIndex] += $valor;

                $texto = $valor > 0 ? $valor : '';
                $pdf->Cell($anchoDia, $altoFila, $texto, 1, 0, 'C');
            }
        }

        $pdf->Cell($anchoTotal, $altoFila, $totalProducto, 1, 1, 'C');
        $totalGeneral += $totalProducto;
    }
}

/* =========================
    FILA DE TOTALES GENERALES
========================= */
$pdf->SetFont('Arial','B',7);
$pdf->Cell($anchoProducto, 6, 'TOTAL', 1, 0, 'C');

for($i=1; $i<=25; $i++){
    $pdf->Cell($anchoDia, 6, $totalesColumnas[$i], 1, 0, 'C');
}
$pdf->Cell($anchoTotal, 6, $totalGeneral, 1, 1, 'C');

/* =========================
    FIRMAS INSTITUCIONALES
========================= */
$pdf->Ln(12);
$pdf->SetFont('Arial','',8);

$anchoFirma = 277 / 3; 
$pdf->Cell($anchoFirma, 5, '_________________________', 0, 0, 'C');
$pdf->Cell($anchoFirma, 5, '_________________________', 0, 0, 'C');
$pdf->Cell($anchoFirma, 5, '_________________________', 0, 1, 'C');

$pdf->Cell($anchoFirma, 4, 'Nombre de la Economa', 0, 0, 'C');
$pdf->Cell($anchoFirma, 4, 'Representante de Beneficiarios', 0, 0, 'C');
$pdf->Cell($anchoFirma, 4, 'Coordinador de la casa', 0, 1, 'C');

/* OUTPUT */
$pdf->Output();
?>