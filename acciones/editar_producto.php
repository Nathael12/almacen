<?php

include("../common/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $nombre_comercial = trim($_POST['nombre_comercial'] ?? '');
    $nombre_comun = trim($_POST['nombre_comun'] ?? '');
    $presentacion = $_POST['presentacion'] ?? null;
    $categoria_producto = trim($_POST['categoria_producto'] ?? '');
    $unidad_id = isset($_POST['unidad_id'])
        ? (int)$_POST['unidad_id']
        : 0;


    // Validar ID

    if ($id <= 0) {

        die("ID de producto no válido.");

    }


    // Validar nombre

    if ($nombre_comercial === '') {

        die("El nombre comercial es obligatorio.");

    }


    // Validar unidad

    if ($unidad_id <= 0) {

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


    // ACTUALIZAR PRODUCTO

    $sql = "UPDATE productos SET

                nombre_comercial = ?,
                nombre_comun = ?,
                presentacion = ?,
                categoria_producto = ?,
                unidad_id = ?

            WHERE id_producto = ?";


    $stmt = $conn->prepare($sql);


    if (!$stmt) {

        die("Error al preparar la consulta: " . $conn->error);

    }


    $stmt->bind_param(
        "ssdsii",
        $nombre_comercial,
        $nombre_comun,
        $presentacion,
        $categoria_producto,
        $unidad_id,
        $id
    );


    if ($stmt->execute()) {

        header("Location: ../productos.php");
        exit;

    } else {

        echo "Error al actualizar el producto: " . $stmt->error;

    }


    $stmt->close();

}

?>