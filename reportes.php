<?php
session_start();
$_SESSION['LOGGED'] = 1;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/registro.css">
</head>
<body>

<?php include("navbar.php"); ?>

<div class="container py-4">

    <!-- TITULO -->
    <div class="titulo-contenedor">
        <h2>Reportes del Almacén</h2>
        <br>
    </div>

    <!-- TARJETAS -->
    <div class="row g-4 justify-content-center">

        <!-- DIARIO -->
        <div class="col-md-4">
            <div class="card shadow border-0 h-100 text-center">
                <div class="card-body">
                    <h5 class="card-title">📅 Reporte Diario</h5>
                    <p class="card-text">Resumen Diario del Almacén</p>
                    <a href="reporte_pdf.php?tipo=diario" 
                       class="btn btn-primary w-100">
                        Generar PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- GENERAL -->
        <div class="col-md-4">
            <div class="card shadow border-0 h-100 text-center">
                <div class="card-body">

                    <h5 class="card-title">📆 Reporte General</h5>
                    <p class="card-text">Resumen General del Almacén</p>

                    <!-- SELECTOR DE MES -->
                    <input type="month" id="mes" class="form-control mb-2"
                           value="<?= date('Y-m') ?>">

                    <!-- BOTON -->
                    <button class="btn btn-primary w-100" onclick="generarReporte()">
                        Generar Reporte General
                    </button>

                </div>
            </div>
        </div>

    </div>

    <!-- BOTON VOLVER -->
    <div class="mt-4">
        <a href="index.php" class="btn btn-secondary">
            ⬅ Volver al inicio
        </a>
    </div>

</div>

<script src="js/bootstrap.bundle.min.js"></script>

<!-- SCRIPT -->
<script>
function generarReporte(){

    let val = document.getElementById('mes').value;

    if(!val){
        alert("Selecciona un mes");
        return;
    }

    let partes = val.split('-'); // [año, mes]

    window.open(
        `reporte_pdf_General.php?anio=${partes[0]}&mes=${partes[1]}`,
        '_blank'
    );
}
</script>

</body>
</html>