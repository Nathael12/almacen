<?php
session_start();

if (!isset($_SESSION['LOGGED']) || $_SESSION['LOGGED'] != 1) {
    // header('Location: login.php');
    // exit();
}

include("common/conexion.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reportes del Almacén</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/registro.css">

    <style>

        body {
            background-image:
                linear-gradient(
                    rgba(0, 0, 0, 0.88),
                    rgba(0, 0, 0, 0.88)
                ),
                url('imagenes/fondoPrin.jpg');

            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;

            min-height: 100vh;
        }

        .main-card {
            background: rgba(15, 15, 15, 0.90);

            border: 1px solid rgba(255, 255, 255, 0.12);

            border-radius: 15px;

            box-shadow:
                0 8px 25px rgba(0, 0, 0, 0.55);

            padding: 2rem;

            margin-top: 0.5rem;

            margin-bottom: 1rem;
        }

        .main-title {
            border-bottom:
                1px solid rgba(255, 255, 255, 0.20);

            padding-bottom: 1rem;
        }

        .main-card h2 {
            color: #ffffff !important;

            font-size: 2rem;

            font-weight: 700;

            text-shadow:
                2px 2px 10px rgba(0, 0, 0, 0.8);

            margin-bottom: 0;
        }

        .report-box {
            background: rgba(35, 35, 35, 0.95);

            border:
                1px solid rgba(255, 255, 255, 0.18);

            border-radius: 12px;

            padding: 1.5rem;

            min-height: 322px;

            box-shadow:
                0 4px 12px rgba(0, 0, 0, 0.35);

            transition:
                all 0.2s ease-in-out;
        }

        .report-box:hover {
            border-color:
                rgba(255, 255, 255, 0.30);

            box-shadow:
                0 7px 18px rgba(0, 0, 0, 0.50);

            transform:
                translateY(-2px);
        }

        .report-box h4 {
            color: #ffffff !important;

            font-size: 1.25rem;

            font-weight: 700;

            text-shadow:
                1px 1px 5px rgba(0, 0, 0, 0.7);

            margin-bottom: 0;
        }

        .report-box p {
            color: #eeeeee !important;

            font-size: 0.95rem;

            line-height: 1.5;

            margin-top: 0.5rem;
        }

        .report-box label {
            color: #ffffff !important;

            font-size: 0.85rem;
        }

        .report-box .badge {
            background-color: #747e88 !important;

            color: #ffffff !important;

            border-radius: 7px;

            font-size: 0.9rem;

            font-weight: 600;

            padding: 0.5rem 0.65rem;
        }

        .report-box .form-control {
            background-color: #f5f5f5;

            color: #222222;

            border:
                1px solid rgba(255, 255, 255, 0.25);

            border-radius: 7px;

            height: 44px;

            padding:
                0.5rem 0.75rem;

            font-size: 0.95rem;
        }

        .report-box .form-control:focus {
            background-color: #ffffff;

            color: #222222;

            border-color: #747e88;

            box-shadow:
                0 0 0 0.15rem
                rgba(116, 126, 136, 0.25);
        }

        .report-box .border-top {
            border-color:
                rgba(255, 255, 255, 0.15) !important;
        }

        .report-box .btn {
            background-color: #747e88;

            border-color: #747e88;

            color: #ffffff;

            border-radius: 7px;

            height: 41px;

            font-size: 0.95rem;

            font-weight: 600;

            transition:
                all 0.2s ease-in-out;
        }

        .report-box .btn:hover {
            background-color: #66707a;

            border-color: #66707a;

            color: #ffffff;

            transform:
                translateY(-1px);

            box-shadow:
                0 4px 10px rgba(0, 0, 0, 0.35);
        }

        @media (max-width: 767px) {

            .main-card {
                padding: 1.2rem;

                margin-top: 0.5rem;
            }

            .main-card h2 {
                font-size: 1.6rem;
            }

            .report-box {
                min-height: auto;

                padding: 1.2rem;
            }

        }

    </style>

</head>

<body>

    <?php include("navbar.php"); ?>

    <div class="container py-4">

        <div class="main-card">

            <!-- =================================================
                 TITULO
            ================================================== -->

            <div class="text-center mb-4 main-title">

                <h2>
                    Reportes del Almacén
                </h2>

            </div>

            <div class="row g-4">

                <!-- =================================================
                     PRODUCTOS INGRESADOS
                ================================================== -->

                <div class="col-md-6">

                    <div class="report-box h-100 d-flex flex-column justify-content-between">

                        <div>

                            <div class="d-flex align-items-center mb-3">

                                <span class="badge me-2">
                                    Productos
                                </span>

                                <h4>
                                    Productos Ingresados
                                </h4>

                            </div>

                            <p>
                                Genera un reporte de los productos
                                ingresados al almacén durante el mes
                                seleccionado.
                            </p>

                            <div class="mb-3">

                                <label
                                    for="mes_productos"
                                    class="form-label fw-bold">

                                    Mes a Consultar:

                                </label>

                                <input
                                    type="month"
                                    id="mes_productos"
                                    class="form-control"
                                    value="<?= date('Y-m') ?>">

                            </div>

                        </div>

                        <div class="pt-3 border-top mt-3">

                            <button
                                type="button"
                                class="btn w-100"
                                onclick="generarReporteProductos()">

                                Generar Reporte de Productos

                            </button>

                        </div>

                    </div>

                </div>

                <!-- =================================================
                     REPORTE CONSOLIDADOR
                ================================================== -->

                <div class="col-md-6">

                    <div class="report-box h-100 d-flex flex-column justify-content-between">

                        <div>

                            <div class="d-flex align-items-center mb-3">

                                <span class="badge me-2">
                                    Mensual
                                </span>

                                <h4>
                                    Reporte Mensual
                                </h4>

                            </div>

                            <p>
                                Consulta el historial de movimientos
                                especificando el mes y año.
                            </p>

                            <div class="mb-3">

                                <label
                                    for="mes"
                                    class="form-label fw-bold">

                                    Mes a Consultar:

                                </label>

                                <input
                                    type="month"
                                    id="mes"
                                    class="form-control"
                                    value="<?= date('Y-m') ?>">

                            </div>

                        </div>

                        <div class="pt-3 mt-3">

                            <button
                                type="button"
                                class="btn w-100"
                                onclick="generarReporteMensual()">

                                Generar Reporte Mensual

                            </button>

                        </div>

                    </div>

                </div>

                <!-- =================================================
                     HISTORIAL SEMANAL
                ================================================== -->

                <div class="col-md-6">

                    <div class="report-box h-100 d-flex flex-column justify-content-between">

                        <div>

                            <div class="d-flex align-items-center mb-3">

                                <span class="badge me-2">
                                    Historial
                                </span>

                                <h4>
                                    Historial Semanal
                                </h4>

                            </div>

                            <p>
                                Consulta el historial de reportes
                                semanales registrados.
                            </p>

                        </div>

                        <div class="pt-3 border-top mt-3">

                            <a
                                href="historial_semanal.php"
                                class="btn w-100">

                                Consultar Historial

                            </a>

                        </div>

                    </div>

                </div>

                <!-- =================================================
                     INVENTARIO ACTUAL
                ================================================== -->

                <div class="col-md-6">

                    <div class="report-box h-100 d-flex flex-column justify-content-between">

                        <div>

                            <div class="d-flex align-items-center mb-3">

                                <span class="badge me-2">
                                    Inventario
                                </span>

                                <h4>
                                    Inventario Actual
                                </h4>

                            </div>

                            <p>
                                Genera directamente el reporte PDF
                                con los productos y cantidades
                                disponibles actualmente en el almacén.
                            </p>

                        </div>

                       <a
                            href="reporte_inventario.php"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn w-100">

                            Generar Inventario

                        </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        /* =====================================================
           REPORTE DE PRODUCTOS
        ===================================================== */

        function generarReporteProductos() {

            let val =
                document.getElementById(
                    'mes_productos'
                ).value;

            if (!val) {

                Swal.fire({ icon: 'warning', title: 'Aviso', text: "Por favor, selecciona un mes válido." });

                return;
            }

            let partes = val.split('-');

            window.open(
                'reporte_pdf.php?anio=' +
                partes[0] +
                '&mes=' +
                partes[1],
                '_blank'
            );

        }

        /* =====================================================
           REPORTE SEMANAL
        ===================================================== */

        function generarReporteSemanal() {

            let fecha =
                document.getElementById(
                    'fecha_semana'
                ).value;

            if (!fecha) {

                Swal.fire({ icon: 'warning', title: 'Aviso', text: "Por favor, selecciona una fecha." });

                return;
            }

            window.open(
                'reporte_pdf_Semanal.php?fecha=' +
                encodeURIComponent(fecha),
                '_blank'
            );

        }

        /* =====================================================
           REPORTE MENSUAL
        ===================================================== */

        function generarReporteMensual() {

            let val =
                document.getElementById(
                    'mes'
                ).value;

            if (!val) {

                Swal.fire({ icon: 'warning', title: 'Aviso', text: "Por favor, selecciona un mes válido." });

                return;
            }

            let partes = val.split('-');

            window.open(
                'reporte_pdf_General.php?anio=' +
                partes[0] +
                '&mes=' +
                partes[1],
                '_blank'
            );

        }

    </script>

</body>
</html>