<?php

date_default_timezone_set('America/Mexico_City');

require('fpdf186/fpdf.php');
include("common/conexion.php");

function pdfTexto($texto)
{
    return iconv('UTF-8', 'Windows-1252//TRANSLIT', $texto);
}

$fecha_referencia = isset($_GET['fecha']) && !empty($_GET['fecha'])
    ? $_GET['fecha']
    : date('Y-m-d');

$timestamp = strtotime($fecha_referencia);

$dia_semana = date(
    'N',
    $timestamp
);

$lunes = date(
    'Y-m-d',
    strtotime(
        "-" . ($dia_semana - 1) . " days",
        $timestamp
    )
);

$domingo = date(
    'Y-m-d',
    strtotime(
        "+6 days",
        strtotime($lunes)
    )
);

$pdf = new FPDF();
$pdf->AddPage();

if (file_exists('imagenes/inpi.png')) {
    $pdf->Image('imagenes/inpi.png', 10, 8, 45);
}

$pdf->Ln(25);

$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(
    0,
    6,
    pdfTexto('INSTITUTO NACIONAL DE LOS PUEBLOS INDÍGENAS'),
    0,
    1,
    'C'
);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(
    0,
    5,
    pdfTexto('CASA COMUNITARIA DEL ESTUDIANTE INDIGENA'),
    0,
    1,
    'C'
);

$pdf->Cell(
    0,
    5,
    pdfTexto('"JACINTO PAAT"'),
    0,
    1,
    'C'
);

$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 11);

$pdf->Cell(
    0,
    6,
    pdfTexto('REQUERIMIENTO SEMANAL DE ALMACEN'),
    0,
    1,
    'C'
);

$pdf->SetFont('Arial', '', 9);

$pdf->Cell(
    0,
    6,
    pdfTexto(
        'Semana del ' .
        date('d/m/Y', strtotime($lunes)) .
        ' al ' .
        date('d/m/Y', strtotime($domingo))
    ),
    0,
    1,
    'C'
);

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(25, 8, 'FECHA', 1, 0, 'C');
$pdf->Cell(25, 8, 'DIA', 1, 0, 'C');
$pdf->Cell(70, 8, 'REQUERIMIENTO', 1, 0, 'C');
$pdf->Cell(20, 8, 'CANT.', 1, 0, 'C');
$pdf->Cell(25, 8, 'SOLICITO', 1, 0, 'C');
$pdf->Cell(25, 8, 'ENTREGO', 1, 1, 'C');

$pdf->SetFont('Arial', '', 9);

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

if (!$result || $result->num_rows === 0) {

    $pdf->Cell(
        190,
        10,
        pdfTexto(
            'No se registraron productos utilizados durante esta semana.'
        ),
        1,
        1,
        'C'
    );

} else {

    while ($row = $result->fetch_assoc()) {

        $time_salida = strtotime(
            $row['fecha_salida']
        );

        $fecha_formateada = date(
            'd/m/Y',
            $time_salida
        );

        $num_dia = date(
            'N',
            $time_salida
        );

        $nombre_dia =
            $dias_semana_es[$num_dia] ?? 'S/D';

        $cantidad =
            (int)$row['cantidad_usada'];

        $total_articulos_semana += $cantidad;

        $pdf->Cell(
            25,
            8,
            $fecha_formateada,
            1,
            0,
            'C'
        );

        $pdf->Cell(
            25,
            8,
            pdfTexto($nombre_dia),
            1,
            0,
            'C'
        );

        $pdf->Cell(
            70,
            8,
            pdfTexto($row['nombre_comercial']),
            1,
            0,
            'L'
        );

        $pdf->Cell(
            20,
            8,
            $cantidad,
            1,
            0,
            'C'
        );

        $pdf->Cell(
            25,
            8,
            '',
            1,
            0,
            'C'
        );

        $pdf->Cell(
            25,
            8,
            '',
            1,
            1,
            'C'
        );
    }

    $pdf->SetFont('Arial', 'B', 9);

    $pdf->Cell(
        120,
        8,
        pdfTexto(
            'TOTAL DE ARTÍCULOS CONSUMIDOS EN LA SEMANA'
        ),
        1,
        0,
        'R'
    );

    $pdf->Cell(
        20,
        8,
        $total_articulos_semana,
        1,
        0,
        'C'
    );

    $pdf->Cell(
        50,
        8,
        '',
        1,
        1,
        'C'
    );
}

$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(
    0,
    5,
    pdfTexto('PERÍODO DE CONSULTA'),
    0,
    1,
    'C'
);

$pdf->SetFont('Arial', '', 9);

$pdf->Cell(
    0,
    5,
    date('d/m/Y', strtotime($lunes)) .
    ' - ' .
    date('d/m/Y', strtotime($domingo)),
    0,
    1,
    'C'
);

$pdf->Output();
?>