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
            background-color: #f4f6f9 !important;
        }
        .main-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 2rem;
        }
        .report-box {
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            padding: 1.5rem;
            transition: all 0.2s ease-in-out;
            background-color: #fff;
        }
        .report-box:hover {
            border-color: #0d6efd;
            box-shadow: 0 6px 15px rgba(13, 110, 253, 0.1);
        }
    </style>
</head>
<body>

<?php include("navbar.php"); ?>

<div class="container py-4">

    <div class="main-card">
        
        <!-- Encabezado Centrado -->
        <div class="text-center mb-4 pb-3 border-bottom">
            <h2 class="fw-bold text-dark mb-0">Reportes del Almacén</h2>
        </div>

        <div class="row g-4">

            <!-- 1. REPORTE DIARIO -->
            <div class="col-md-6">
                <div class="report-box h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary fs-6 p-2 me-2">Diario</span>
                            <h4 class="fw-bold text-dark mb-0">Reporte del Día</h4>
                        </div>
                        <p class="text-secondary">
                            Genera un informe con los movimientos del día de hoy en el inventario.
                        </p>
                    </div>
                    
                    <div class="pt-3 border-top mt-3">
                        <a href="reporte_pdf.php?tipo=diario" target="_blank" class="btn btn-primary w-100 py-2 fw-semibold">
                            Descargar PDF Diario
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. REPORTE MENSUAL -->
            <div class="col-md-6">
                <div class="report-box h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-dark fs-6 p-2 me-2">Mensual</span>
                            <h4 class="fw-bold text-dark mb-0">Reporte Consolidador</h4>
                        </div>
                        <p class="text-secondary mb-3">
                            Consulta el historial de movimientos especificando el mes y año.
                        </p>
                        
                        <div class="mb-3">
                            <label for="mes" class="form-label small fw-bold text-secondary">Mes a Consultar:</label>
                            <input type="month" id="mes" class="form-control" value="<?= date('Y-m') ?>">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button class="btn btn-dark w-100 py-2 fw-semibold" onclick="generarReporteMensual()">
                            Generar Reporte Mensual
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<script src="js/bootstrap.bundle.min.js"></script>

<script>
function generarReporteMensual() {
    let val = document.getElementById('mes').value;

    if (!val) {
        alert("Por favor, selecciona un mes válido.");
        return;
    }

    let partes = val.split('-');

    window.open(
        `reporte_pdf_General.php?anio=${partes[0]}&mes=${partes[1]}`,
        '_blank'
    );
}
</script>

</body>
</html>