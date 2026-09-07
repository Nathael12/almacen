<?php
include("common/conexion.php");

// CONSULTAR FECHAS DE SALIDAS

$sql = "
SELECT DISTINCT DATE(fecha_salida) AS fecha
FROM salidas
ORDER BY fecha DESC
";

$result = $conn->query($sql);


// CREAR LISTA DE SEMANAS


$semanas = [];

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $fecha = $row['fecha'];

        $timestamp = strtotime($fecha);

        // Número del día de la semana
        // 1 = lunes
        // 7 = domingo
        $dia_semana = date('N', $timestamp);


        // OBTENER LUNES DE LA SEMANA
    

        $lunes = date(
            'Y-m-d',
            strtotime("-" . ($dia_semana - 1) . " days", $timestamp)
        );

        // OBTENER DOMINGO DE LA SEMANA
      

        $domingo = date(
            'Y-m-d',
            strtotime("+6 days", strtotime($lunes))
        );

        // GUARDAR SEMANA

        if (!isset($semanas[$lunes])) {

            $semanas[$lunes] = [
                'lunes' => $lunes,
                'domingo' => $domingo
            ];
        }
    }
}

// ORDENAR SEMANAS DE LA MÁS RECIENTE
// A LA MÁS ANTIGUA


krsort($semanas);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Historial de Reportes Semanales</title>

    <link
        rel="stylesheet"
        href="css/bootstrap.min.css"
    >

    <link
        rel="stylesheet"
        href="css/registro.css"
    >

    <style>

        body {
            background-color: #f4f6f9 !important;
        }

        .main-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 2rem;
        }

        .table {
            vertical-align: middle;
        }

        .table thead th {
            text-align: center;
        }

        .btn-pdf {
            min-width: 100px;
        }

    </style>

</head>

<body>

<?php include("navbar.php"); ?>


<div class="container py-4">

    <div class="main-card">

        <!-- ENCABEZADO -->

        <div class="text-center mb-4 pb-3 border-bottom">

            <h2 class="fw-bold text-dark mb-0">
                Historial de Reportes Semanales
            </h2>

            <p class="text-secondary mt-2 mb-0">
                Consulta los reportes semanales registrados en el almacén.
            </p>

        </div>


        <?php if (empty($semanas)): ?>

            <!-- SIN REGISTROS -->

            <div class="alert alert-info text-center">

                No existen registros para mostrar.

            </div>


        <?php else: ?>


            <!-- TABLA -->

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

                            <!-- NUMERO -->

                            <td class="text-center">

                                <?php echo $contador; ?>

                            </td>


                            <!-- SEMANA -->

                            <td class="text-center fw-semibold">

                                Semana <?php echo date(
                                    'W',
                                    strtotime($semana['lunes'])
                                ); ?>

                            </td>


                            <!-- FECHA INICIAL -->

                            <td class="text-center">

                                <?php

                                echo date(
                                    'd/m/Y',
                                    strtotime($semana['lunes'])
                                );

                                ?>

                            </td>


                            <!-- FECHA FINAL -->

                            <td class="text-center">

                                <?php

                                echo date(
                                    'd/m/Y',
                                    strtotime($semana['domingo'])
                                );

                                ?>

                            </td>


                            <!-- ACCION -->

                            <td class="text-center">

                             <a href="reporte_pdf_Semanal.php?fecha=<?php echo urlencode($semana['lunes']);
                                 ?>" target="_blank" ...
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