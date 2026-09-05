<?php
include("../common/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
    $proveedor_id = isset($_POST['proveedor_id']) ? intval($_POST['proveedor_id']) : 0;
    $fecha_entrada = $_POST['fecha_entrada'] ?? '';
    $fecha_caducidad = $_POST['fecha_caducidad'] ?? '';
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

    // Validaciones para evitar registros en blanco
    if (
        $producto_id <= 0 ||
        $proveedor_id <= 0 ||
        empty($fecha_entrada) ||
        empty($fecha_caducidad) ||
        $cantidad <= 0
    ) {
        header("Location: ../lotes.php?mensaje=datos_invalidos");
        exit;
    }

    // La fecha de caducidad no puede ser menor que la fecha de entrada
    if ($fecha_caducidad < $fecha_entrada) {
        header("Location: ../lotes.php?mensaje=fecha_invalida");
        exit;
    }

    /*
        Verificamos si ya existe un lote activo con:

        - El mismo producto
        - El mismo proveedor
        - La misma fecha de entrada
        - La misma fecha de caducidad

        Si existe, aumentamos la cantidad.

        Si no existe, creamos un lote nuevo.
    */

    $sql_buscar = "
        SELECT id_lote, cantidad
        FROM lotes
        WHERE producto_id = ?
        AND proveedor_id = ?
        AND fecha_entrada = ?
        AND fecha_caducidad = ?
        AND estado = 1
        AND fecha_salida IS NULL
        LIMIT 1
    ";

    $stmt_buscar = $conn->prepare($sql_buscar);

    if (!$stmt_buscar) {
        die("Error en la consulta: " . $conn->error);
    }

    $stmt_buscar->bind_param(
        "iiss",
        $producto_id,
        $proveedor_id,
        $fecha_entrada,
        $fecha_caducidad
    );

    if (!$stmt_buscar->execute()) {
        die("Error al ejecutar la consulta: " . $stmt_buscar->error);
    }

    $resultado = $stmt_buscar->get_result();

    if ($resultado->num_rows > 0) {

        // Ya existe un lote con los mismos datos
        $lote_existente = $resultado->fetch_assoc();

        $id_lote = intval($lote_existente['id_lote']);

        $sql_actualizar = "
            UPDATE lotes
            SET cantidad = cantidad + ?
            WHERE id_lote = ?
            AND estado = 1
            AND fecha_salida IS NULL
        ";

        $stmt_actualizar = $conn->prepare($sql_actualizar);

        if (!$stmt_actualizar) {
            die("Error al preparar actualización: " . $conn->error);
        }

        $stmt_actualizar->bind_param(
            "ii",
            $cantidad,
            $id_lote
        );

        if ($stmt_actualizar->execute()) {
            header("Location: ../lotes.php?mensaje=cantidad_actualizada");
            exit;
        } else {
            die("Error al actualizar la cantidad: " . $stmt_actualizar->error);
        }

    } else {

        // No existe, creamos un lote nuevo

        $sql = "
            INSERT INTO lotes
            (
                producto_id,
                proveedor_id,
                fecha_entrada,
                fecha_caducidad,
                cantidad,
                estado
            )
            VALUES (?, ?, ?, ?, ?, 1)
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error al preparar la consulta: " . $conn->error);
        }

        $stmt->bind_param(
            "iissi",
            $producto_id,
            $proveedor_id,
            $fecha_entrada,
            $fecha_caducidad,
            $cantidad
        );

        if ($stmt->execute()) {
            header("Location: ../lotes.php?mensaje=lote_agregado");
            exit;
        } else {
            die("Error al insertar en la base de datos: " . $stmt->error);
        }
    }

} else {

    header("Location: ../lotes.php");
    exit;
}
?>