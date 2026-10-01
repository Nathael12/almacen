
<?php

// Evitar que PHP mande cualquier salida antes del PDF
ob_start();

date_default_timezone_set('America/Mexico_City');

session_start();

if (!isset($_SESSION['usuario'])) {
    ob_end_clean();
    header("Location: login.php");
    exit();
}

require('fpdf186/fpdf.php');
include("common/conexion.php");

// =====================================================
// FUNCIÓN PARA TEXTO EN PDF
// =====================================================

function pdfTexto($texto)
{
    return iconv(
        'UTF-8',
        'Windows-1252//TRANSLIT',
        $texto
    );
}

// =====================================================
// CONSULTA DEL INVENTARIO
// =====================================================

$sql = "
    SELECT 
        p.nombre_comercial,
        IFNULL(SUM(l.cantidad), 0) AS cantidad
    FROM productos p
    LEFT JOIN lotes l
        ON p.id_producto = l.producto_id
        AND l.estado = 1
        AND l.cantidad > 0
    WHERE p.estado = 1
    GROUP BY 
        p.id_producto,
        p.nombre_comercial
    ORDER BY p.nombre_comercial ASC
";

$result = $conn->query($sql);

// =====================================================
// CREAR PDF
// =====================================================

$pdf = new FPDF(
    'P',
    'mm',
    'A4'
);

$pdf->AddPage();

// =====================================================
// LOGO
// =====================================================

if (file_exists('imagenes/inpi.png')) {

    $pdf->Image(
        'imagenes/inpi.png',
        10,
        8,
        45
    );
}

// =====================================================
// ENCABEZADO
// =====================================================

$pdf->Ln(25);

$pdf->SetFont(
    'Arial',
    'B',
    12
);

$pdf->Cell(
    0,
    6,
    pdfTexto(
        'INSTITUTO NACIONAL DE LOS PUEBLOS INDÍGENAS'
    ),
    0,
    1,
    'C'
);

$pdf->SetFont(
    'Arial',
    '',
    10
);

$pdf->Cell(
    0,
    5,
    pdfTexto(
        'CASA COMUNITARIA DEL ESTUDIANTE INDIGENA'
    ),
    0,
    1,
    'C'
);

$pdf->Cell(
    0,
    5,
    pdfTexto(
        '"JACINTO PAAT"'
    ),
    0,
    1,
    'C'
);

$pdf->Ln(4);

// =====================================================
// TÍTULO
// =====================================================

$pdf->SetFont(
    'Arial',
    'B',
    12
);

$pdf->Cell(
    0,
    7,
    pdfTexto(
        'PRODUCTOS DEL INVENTARIO'
    ),
    0,
    1,
    'C'
);

$pdf->SetFont(
    'Arial',
    '',
    9
);

$pdf->Cell(
    0,
    6,
    pdfTexto(
        'Productos disponibles actualmente en el almacén'
    ),
    0,
    1,
    'C'
);

$pdf->Ln(6);

// =====================================================
// TABLA
// =====================================================

$pdf->SetFont(
    'Arial',
    'B',
    10
);

$pdf->Cell(
    135,
    9,
    pdfTexto(
        'NOMBRE DEL PRODUCTO'
    ),
    1,
    0,
    'C'
);

$pdf->Cell(
    35,
    9,
    pdfTexto(
        'CANTIDAD'
    ),
    1,
    1,
    'C'
);

// =====================================================
// PRODUCTOS
// =====================================================

$pdf->SetFont(
    'Arial',
    '',
    9
);

$total_productos = 0;

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $nombre = $row['nombre_comercial'];

        $cantidad = (int)$row['cantidad'];

        $total_productos += $cantidad;

        $pdf->Cell(
            135,
            8,
            pdfTexto($nombre),
            1,
            0,
            'L'
        );

        $pdf->Cell(
            35,
            8,
            $cantidad,
            1,
            1,
            'C'
        );
    }

    // =================================================
    // TOTAL
    // =================================================

    $pdf->SetFont(
        'Arial',
        'B',
        9
    );

    $pdf->Cell(
        135,
        8,
        pdfTexto(
            'TOTAL DE PRODUCTOS EN INVENTARIO'
        ),
        1,
        0,
        'R'
    );

    $pdf->Cell(
        35,
        8,
        $total_productos,
        1,
        1,
        'C'
    );

} else {

    $pdf->Cell(
        170,
        10,
        pdfTexto(
            'No hay productos disponibles en el inventario.'
        ),
        1,
        1,
        'C'
    );
}

// =====================================================
// FECHA
// =====================================================

$pdf->Ln(8);

$pdf->SetFont(
    'Arial',
    'I',
    9
);

$pdf->Cell(
    0,
    5,
    pdfTexto(
        'Reporte generado el: ' .
        date('d/m/Y H:i')
    ),
    0,
    1,
    'R'
);

// =====================================================
// LIMPIAR CUALQUIER SALIDA ANTES DEL PDF
// =====================================================

ob_end_clean();

// =====================================================
// MOSTRAR PDF
// =====================================================

$pdf->Output(
    'I',
    'reporte_inventario.pdf'
);

exit;

