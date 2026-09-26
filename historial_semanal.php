<?php

include("common/conexion.php");

$sql = "
SELECT DISTINCT DATE(fecha_salida) AS fecha
FROM salidas
ORDER BY fecha DESC
";

$result = $conn->query($sql);

$semanas = [];

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $fecha = $row['fecha'];

        $timestamp = strtotime($fecha);

        $dia_semana = date('N', $timestamp);

        $lunes = date(
            'Y-m-d',
            strtotime("-" . ($dia_semana - 1) . " days", $timestamp)
        );

        $domingo = date(
            'Y-m-d',
            strtotime("+6 days", strtotime($lunes))
        );

        if (!isset($semanas[$lunes])) {

            $semanas[$lunes] = [
                'lunes' => $lunes,
                'domingo' => $domingo
            ];
        }
    }
}

krsort($semanas);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Historial de Reportes Semanales</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/registro.css">

    <style>

        body {
            background-image: linear-gradient(
                rgba(0, 0, 0, 0.88),
                rgba(0, 0, 0, 0.88)
            ), url('imagenes/fondoPrin.jpg');

            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }

        .main-card {
            background: rgba(15, 15, 15, 0.90);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.55);
            padding: 2rem;
        }

        .main-card h2 {
            color: white !important;
            font-weight: 700;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.7);
        }

        .main-card p {
            color: #eeeeee !important;
        }

        .table {
            vertical-align: middle;
        }

        .table thead th {
            text-align: center;
        }

        .btn-pdf {
            min-width: 100px;
            background-color: #747e88 !important;
            border-color: #747e88 !important;
            color: white !important;
        }

        .btn-pdf:hover {
            background-color: #66707a !important;
            border-color: #66707a !important;
            color: white !important;
        }

        .btn-regresar {
            background-color: #747e88;
            border-color: #747e88;
            color: white;
        }

        .btn-regresar:hover {
            background-color: #66707a;
            border-color: #66707a;
            color: white;
        }

    </style>

</head>

<body>

<?php include("navbar.php"); ?>

<div class="container py-4">

    <div class="main-card">

        <div class="text-center mb-4 pb-3 border-bottom">

            <h2 class="mb-0">
                Historial de Reportes Semanales
            </h2>

            <p class="mt-2 mb-0">
                Consulta los reportes semanales registrados en el almacén.
            </p>

        </div>

        <div class="mb-4">

            <a
                href="reportes.php"
                class="btn btn-regresar"
            >
                ← Regresar a Reportes
            </a>

        </div>

        <?php if (empty($semanas)): ?>

            <div class="alert alert-info text-center">
                No existen registros para mostrar.
            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="table-dark">

                        <tr>

                            <th>#</th>
                            <th>Semana</th>
                            <th>Fecha inicial</th>
                            <th>Fecha final</th>
                            <th>Acción</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    $contador = 1;

                    foreach ($semanas as $semana):

                    ?>

                        <tr>

                            <td class="text-center">
                                <?php echo $contador; ?>
                            </td>

                            <td class="text-center fw-semibold">
                                Semana <?php echo date(
                                    'W',
                                    strtotime($semana['lunes'])
                                ); ?>
                            </td>

                            <td class="text-center">

                                <?php

                                echo date(
                                    'd/m/Y',
                                    strtotime($semana['lunes'])
                                );

                                ?>

                            </td>

                            <td class="text-center">

                                <?php

                                echo date(
                                    'd/m/Y',
                                    strtotime($semana['domingo'])
                                );

                                ?>

                            </td>

                            <td class="text-center">

                                <a href="reporte_pdf_Semanal.php?fecha=<?php echo urlencode($semana['lunes']); ?>"
                                   target="_blank"
                                   class="btn btn-primary btn-sm btn-pdf"
                                >
                                    Ver PDF
                                </a>

                            </td>

                        </tr>

                    <?php

                    $contador++;

                    endforeach;

                    ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>

<script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>