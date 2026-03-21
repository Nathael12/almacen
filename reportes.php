<?php
    session_start();
    $_SESSION['LOGGED'] = 1;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reportes</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
    </head> 
    <body>
    <?php include("navbar.php"); ?>    
        <h2 class="text-center mb-4">Reportes del Almacen</h2>
        <a href="index.php" class="btn btn-secondary ms-2">Inicio</a>
    </body>
</html>