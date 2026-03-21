<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Sistema de Almacén</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/index.css">

</head>
    <body class="bg-light">

    <?php include("navbar.php"); ?>
    
        <header class="header-bar text-center">
            <h1 class="m-0">Sistema de Control de Almacén</h1>
        </header>

        <div class="container py-5">

            <div class="row justify-content-center">

                <div class="col-md-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body text-center">
                            <h4>Productos</h4>
                            <img src="imagenes/producto.png" alt="productos" class="img-fluid mb-3" style="max-height: 100px;">
                            <a href="productos.php" class="btn btn-primary w-100">Entrar</a>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <div class="col-md-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body text-center">
                            <h4>Registros</h4>
                            <img src="imagenes/registro.png" alt="registro" class="img-fluid mb-3" style="max-height: 100px;">
                            <a href="registro.php" class="btn btn-danger w-100">Entrar</a>
                        </div>
                    </div>
                </div>

            </div>
            <br>
            <div class="row justify-content-center">

                <div class="col-md-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body text-center">
                            <h4>Reporte</h4>
                            <img src="imagenes/reporte.png" alt="reporte" class="img-fluid mb-3" style="max-height: 100px;">
                            <a href="reportes.php" class="btn btn-warning w-100 text-white">Entrar</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </body>
</html>