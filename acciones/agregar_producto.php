<?php

include("../common/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre_comercial = trim($_POST['nombre_comercial'] ?? '');
    $nombre_comun = trim($_POST['nombre_comun'] ?? '');
    $presentacion = $_POST['presentacion'] ?? null;
    $categoria = trim($_POST['categoria_producto'] ?? '');
    $unidad_id = $_POST['unidad_id'] ?? '';

    // Validar nombre comercial
    if ($nombre_comercial === '') {
        die("El nombre comercial es obligatorio.");
    }

    // Validar unidad
    if ($unidad_id === '') {
        die("Debe seleccionar una unidad de medida.");
    }

    // Validar presentación
    if ($presentacion === '' || $presentacion === null) {

        $presentacion = null;

    } else {

        $presentacion = (float)$presentacion;

        if ($presentacion <= 0) {
            die("La presentación debe ser mayor que 0.");
        }
    }

    $unidad_id = (int)$unidad_id;

    $sql = "INSERT INTO productos
            (
                nombre_comercial,
                nombre_comun,
                presentacion,
                categoria_producto,
                unidad_id
            )
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        die("Error al preparar la consulta: " . $conn->error);

    }

    $stmt->bind_param(
        "ssdsi",
        $nombre_comercial,
        $nombre_comun,
        $presentacion,
        $categoria,
        $unidad_id
    );

    if ($stmt->execute()) {

        header("Location: ../productos.php");
        exit;

    } else {

        echo "Error al guardar el producto: " . $stmt->error;

    }

    $stmt->close();

}

?>