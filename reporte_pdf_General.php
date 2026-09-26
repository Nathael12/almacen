<?php

date_default_timezone_set('America/Mexico_City');

require('fpdf186/fpdf.php');
include("common/conexion.php");

$conn->query("SET time_zone = '-06:00';");

function pdfTexto($texto)
{
    return iconv('UTF-8', 'Windows-1252//TRANSLIT', $texto);
}

$mes = isset($_GET['mes'])
    ? (int)$_GET['mes']
    : (int)date('m');

$anio = isset($_GET['anio'])
    ? (int)$_GET['anio']
    : (int)date('Y');

if ($mes < 1 || $mes > 12) {
    $mes = (int)date('m');
}

if ($anio < 2000 || $anio > 2100) {
    $anio = (int)date('Y');
}

$inicioPeriodo = "01";

$finPeriodo = date(
    "t",
    strtotime("$anio-$mes-01")
);

$mesNombre = [
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

$sql = "
    SELECT
        p.id_producto,
        p.nombre_comercial,
        s.fecha_salida,
        SUM(s.cantidad_usada) AS total_usado
    FROM salidas s
    INNER JOIN productos p
        ON p.id_producto = s.producto_id
    WHERE MONTH(s.fecha_salida) = $mes
    AND YEAR(s.fecha_salida) = $anio
    GROUP BY
        p.id_producto,
        p.nombre_comercial,
        s.fecha_salida
    ORDER BY
        p.nombre_comercial,
        s.fecha_salida
";

$result = $conn->query($sql);

$reporte = [];

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $producto = $row['id_producto'];
        $nombre = $row['nombre_comercial'];
        $fechaStr = $row['fecha_salida'];

        $cantidad_dia =
            (int)$row['total_usado'];

        $timestamp =
            strtotime($fechaStr);

        $diaSemana =
            (int)date('N', $timestamp);

        if ($diaSemana <= 5) {

            $diaMes =
                (int)date('d', $timestamp);

            $semana =
                (int)ceil($diaMes / 7);

            if ($semana > 5) {
                $semana = 5;
            }

            $reporte[$producto]['nombre'] =
                $nombre;

            if (
                !isset(
                    $reporte[$producto]['datos']
                    [$semana]
                    [$diaSemana]
                )
            ) {
                $reporte[$producto]['datos']
                    [$semana]
                    [$diaSemana] = 0;
            }

            $reporte[$producto]['datos']
                [$semana]
                [$diaSemana] += $cantidad_dia;
        }
    }
}

$pdf = new FPDF(
    'L',
    'mm',
    'A4'
);

$pdf->AddPage();

$pdf->SetMargins(
    10,
    10,
    10
);

$pdf->SetAutoPageBreak(
    true,
    15
);

if (file_exists('imagenes/inpi.png')) {
    $pdf->Image(
        'imagenes/inpi.png',
        15,
        10,
        38
    );
}

$pdf->SetFont(
    'Arial',
    'B',
    10
);

$pdf->Cell(
    0,
    4,
    pdfTexto(
        'Programa de Apoyo a la Educación Indígena'
    ),
    0,
    1,
    'C'
);

$pdf->Cell(
    0,
    4,
    pdfTexto(
        'Casa o Comedor Comunitario del Estudiante Indígena'
    ),
    0,
    1,
    'C'
);

$pdf->Ln(2);

$pdf->SetFont(
    'Arial',
    'B',
    11
);

$pdf->Cell(
    0,
    5,
    pdfTexto(
        'Anexo A2.1 Registro Diario de Salidas de Almacén'
    ),
    0,
    1,
    'C'
);

$pdf->Ln(4);

$pdf->SetFont(
    'Arial',
    '',
    8
);

$pdf->Cell(
    25,
    4,
    'CCPI: ',
    0,
    0,
    'L'
);

$pdf->SetFont(
    'Arial',
    'B',
    8
);

$pdf->Cell(
    95,
    4,
    'HOPELCHEN.',
    'B',
    0,
    'L'
);

$pdf->SetFont(
    'Arial',
    '',
    8
);

$pdf->Cell(
    20,
    4,
    'CLAVE: ',
    0,
    0,
    'R'
);

$pdf->SetFont(
    'Arial',
    'B',
    8
);

$pdf->Cell(
    0,
    4,
    '040060001A03',
    'B',
    1,
    'L'
);

$pdf->SetFont(
    'Arial',
    '',
    8
);

$pdf->Cell(
    25,
    5,
    'MUNICIPIO: ',
    0,
    0,
    'L'
);

$pdf->SetFont(
    'Arial',
    'B',
    8
);

$pdf->Cell(
    95,
    5,
    'HOPELCHEN.',
    'B',
    0,
    'L'
);

$pdf->SetFont(
    'Arial',
    '',
    8
);

$pdf->Cell(
    20,
    5,
    'LOCALIDAD: ',
    0,
    0,
    'R'
);

$pdf->SetFont(
    'Arial',
    'B',
    8
);

$pdf->Cell(
    0,
    5,
    'HOPELCHEN.',
    'B',
    1,
    'L'
);

$pdf->SetFont(
    'Arial',
    '',
    8
);

$pdf->Cell(
    55,
    5,
    'NOMBRE DE LA CASA O COMEDOR: ',
    0,
    0,
    'L'
);

$pdf->SetFont(
    'Arial',
    'B',
    8
);

$pdf->Cell(
    0,
    5,
    pdfTexto(
        'CASA COMUNITARIA DEL ESTUDIANTE INDIGENA "JACINTO PAAT"'
    ),
    'B',
    1,
    'L'
);

$textoPeriodo =
    "Registro de salidas del almacén del periodo: del " .
    $inicioPeriodo .
    " de " .
    $mesNombre[$mes] .
    " al " .
    $finPeriodo .
    " de " .
    $mesNombre[$mes] .
    " de " .
    $anio .
    ".";

$pdf->SetFont(
    'Arial',
    '',
    8
);

$pdf->Cell(
    0,
    6,
    pdfTexto($textoPeriodo),
    0,
    1,
    'L'
);

$pdf->Ln(3);

$anchoProducto = 80;
$anchoDia = 7;
$anchoTotal = 22;

$pdf->SetFont(
    'Arial',
    'B',
    8
);

$xInicioTabla =
    $pdf->GetX();

$yInicioTabla =
    $pdf->GetY();

$pdf->Cell(
    $anchoProducto,
    12,
    pdfTexto(
        'Descripción del artículo (nombre, marca, cantidad)'
    ),
    1,
    0,
    'C'
);

for ($i = 1; $i <= 5; $i++) {

    $pdf->Cell(
        5 * $anchoDia,
        6,
        $i . "° semana",
        1,
        0,
        'C'
    );
}

$pdf->Cell(
    $anchoTotal,
    12,
    'Total',
    1,
    0,
    'C'
);

$pdf->SetXY(
    $xInicioTabla + $anchoProducto,
    $yInicioTabla + 6
);

$pdf->SetFont(
    'Arial',
    'B',
    5.5
);

$diasLetras = [
    'Lunes',
    'Martes',
    'Miérc.',
    'Jueves',
    'Viernes'
];

for ($s = 1; $s <= 5; $s++) {

    foreach ($diasLetras as $d) {

        $pdf->Cell(
            $anchoDia,
            6,
            pdfTexto($d),
            1,
            0,
            'C'
        );
    }
}

$pdf->SetY(
    $yInicioTabla + 12
);

$pdf->SetFont(
    'Arial',
    '',
    7
);

$totalesColumnas =
    array_fill(
        1,
        25,
        0
    );

$totalGeneral = 0;

if (count($reporte) === 0) {

    $pdf->Cell(
        277,
        10,
        pdfTexto(
            'No se encontraron registros de salidas en el mes seleccionado.'
        ),
        1,
        1,
        'C'
    );

} else {

    foreach ($reporte as $producto) {

        $yInicioRow =
            $pdf->GetY();

        $xInicioRow =
            $pdf->GetX();

        $pdf->MultiCell(
            $anchoProducto,
            5,
            pdfTexto($producto['nombre']),
            1,
            'L'
        );

        $yFinalRow =
            $pdf->GetY();

        $altoFila =
            $yFinalRow - $yInicioRow;

        $pdf->SetXY(
            $xInicioRow + $anchoProducto,
            $yInicioRow
        );

        $totalProducto = 0;

        for ($semana = 1; $semana <= 5; $semana++) {

            for ($dia = 1; $dia <= 5; $dia++) {

                $valor =
                    $producto['datos']
                    [$semana]
                    [$dia] ?? 0;

                $totalProducto += $valor;

                $colIndex =
                    (($semana - 1) * 5) + $dia;

                $totalesColumnas[$colIndex] +=
                    $valor;

                $texto =
                    $valor > 0
                    ? $valor
                    : '';

                $pdf->Cell(
                    $anchoDia,
                    $altoFila,
                    $texto,
                    1,
                    0,
                    'C'
                );
            }
        }

        $pdf->Cell(
            $anchoTotal,
            $altoFila,
            $totalProducto,
            1,
            1,
            'C'
        );

        $totalGeneral +=
            $totalProducto;
    }
}

$pdf->SetFont(
    'Arial',
    'B',
    7
);

$pdf->Cell(
    $anchoProducto,
    6,
    'TOTAL',
    1,
    0,
    'C'
);

for ($i = 1; $i <= 25; $i++) {

    $pdf->Cell(
        $anchoDia,
        6,
        $totalesColumnas[$i],
        1,
        0,
        'C'
    );
}

$pdf->Cell(
    $anchoTotal,
    6,
    $totalGeneral,
    1,
    1,
    'C'
);

$pdf->Ln(12);

$pdf->SetFont(
    'Arial',
    '',
    8
);

$anchoFirma =
    277 / 3;

$pdf->Cell(
    $anchoFirma,
    5,
    '_________________________',
    0,
    0,
    'C'
);

$pdf->Cell(
    $anchoFirma,
    5,
    '_________________________',
    0,
    0,
    'C'
);

$pdf->Cell(
    $anchoFirma,
    5,
    '_________________________',
    0,
    1,
    'C'
);

$pdf->Cell(
    $anchoFirma,
    4,
    'Nombre de la Economa',
    0,
    0,
    'C'
);

$pdf->Cell(
    $anchoFirma,
    4,
    'Representante de Beneficiarios',
    0,
    0,
    'C'
);

$pdf->Cell(
    $anchoFirma,
    4,
    'Coordinador de la casa',
    0,
    1,
    'C'
);

$pdf->Output();
?>