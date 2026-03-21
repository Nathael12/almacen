<?php
    include("../common/conexion.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nombre_comercial = $_POST['nombre_comercial'];
        $nombre_comun = $_POST['nombre_comun'];
        $categoria = $_POST['categoria_producto'];
        $unidad_id = $_POST['unidad_id'];

        $sql = "INSERT INTO productos 
                (nombre_comercial, nombre_comun, categoria_producto, unidad_id)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nombre_comercial, $nombre_comun, $categoria, $unidad_id);

        if ($stmt->execute()) {
            header("Location: ../productos.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }
    $unidades = $conn->query("SELECT * FROM unidad_medida");
?>