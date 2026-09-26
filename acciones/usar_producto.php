<?php

session_start();

include("../common/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['mensaje'] = "Solicitud no válida.";
    $_SESSION['tipo_mensaje'] = "danger";

    header("Location: ../productos.php");
    exit();
}

$id_producto = isset($_POST['id_producto'])
    ? intval($_POST['id_producto'])
    : 0;

$id_lote = isset($_POST['id_lote'])
    ? intval($_POST['id_lote'])
    : 0;

$cantidad_usada = isset($_POST['cantidad_usada'])
    ? intval($_POST['cantidad_usada'])
    : 0;

$fecha_uso = isset($_POST['fecha_uso']) && !empty($_POST['fecha_uso'])
    ? $_POST['fecha_uso']
    : date('Y-m-d');

if (
    $id_producto <= 0 ||
    $id_lote <= 0 ||
    $cantidad_usada <= 0
) {

    $_SESSION['mensaje'] =
        "Por favor selecciona un producto, un lote y una cantidad válida.";

    $_SESSION['tipo_mensaje'] = "danger";

    header("Location: ../productos.php");
    exit();
}

$fecha_validada = DateTime::createFromFormat('Y-m-d', $fecha_uso);

if (
    !$fecha_validada ||
    $fecha_validada->format('Y-m-d') !== $fecha_uso
) {

    $_SESSION['mensaje'] =
        "La fecha de uso no es válida.";

    $_SESSION['tipo_mensaje'] = "danger";

    header("Location: ../productos.php");
    exit();
}

$fecha_actual = $fecha_uso;

$conn->begin_transaction();

try {

    $sql_lote = "
        SELECT
            id_lote,
            producto_id,
            cantidad,
            estado
        FROM lotes
        WHERE id_lote = ?
        AND producto_id = ?
        FOR UPDATE
    ";

    $stmt_lote = $conn->prepare($sql_lote);

    if (!$stmt_lote) {
        throw new Exception(
            "Error al preparar la consulta del lote: " .
            $conn->error
        );
    }

    $stmt_lote->bind_param(
        "ii",
        $id_lote,
        $id_producto
    );

    $stmt_lote->execute();

    $result_lote = $stmt_lote->get_result();

    if ($result_lote->num_rows === 0) {
        throw new Exception(
            "El lote seleccionado no pertenece al producto."
        );
    }

    $lote = $result_lote->fetch_assoc();

    if (intval($lote['estado']) !== 1) {
        throw new Exception(
            "El lote seleccionado ya no está disponible."
        );
    }

    $cantidad_disponible = intval(
        $lote['cantidad']
    );

    if ($cantidad_usada > $cantidad_disponible) {
        throw new Exception(
            "No puedes utilizar $cantidad_usada unidades. " .
            "Solo hay $cantidad_disponible unidades disponibles."
        );
    }

    $nueva_cantidad =
        $cantidad_disponible -
        $cantidad_usada;

    $sql_salida = "
        INSERT INTO salidas
        (
            producto_id,
            cantidad_usada,
            fecha_salida
        )
        VALUES (?, ?, ?)
    ";

    $stmt_salida = $conn->prepare($sql_salida);

    if (!$stmt_salida) {
        throw new Exception(
            "Error al preparar la salida: " .
            $conn->error
        );
    }

    $stmt_salida->bind_param(
        "iis",
        $id_producto,
        $cantidad_usada,
        $fecha_actual
    );

    if (!$stmt_salida->execute()) {
        throw new Exception(
            "Error al registrar la salida: " .
            $stmt_salida->error
        );
    }

    if ($nueva_cantidad > 0) {

        $sql_actualizar = "
            UPDATE lotes
            SET cantidad = ?
            WHERE id_lote = ?
            AND estado = 1
        ";

        $stmt_actualizar = $conn->prepare($sql_actualizar);

        if (!$stmt_actualizar) {
            throw new Exception(
                "Error al preparar la actualización del lote: " .
                $conn->error
            );
        }

        $stmt_actualizar->bind_param(
            "ii",
            $nueva_cantidad,
            $id_lote
        );

        if (!$stmt_actualizar->execute()) {
            throw new Exception(
                "Error al actualizar la cantidad del lote."
            );
        }

        $mensaje_final =
            "Salida registrada correctamente. " .
            "Se utilizaron $cantidad_usada unidades. " .
            "Quedan $nueva_cantidad unidades disponibles.";

    } else {

        $sql_cerrar = "
            UPDATE lotes
            SET
                cantidad = 0,
                estado = 0,
                fecha_salida = ?
            WHERE id_lote = ?
            AND estado = 1
        ";

        $stmt_cerrar = $conn->prepare($sql_cerrar);

        if (!$stmt_cerrar) {
            throw new Exception(
                "Error al preparar el cierre del lote: " .
                $conn->error
            );
        }

        $stmt_cerrar->bind_param(
            "si",
            $fecha_actual,
            $id_lote
        );

        if (!$stmt_cerrar->execute()) {
            throw new Exception(
                "Error al cerrar el lote."
            );
        }

        $mensaje_final =
            "Salida registrada correctamente. " .
            "Se utilizaron todas las unidades del lote.";
    }

    $conn->commit();

    $_SESSION['mensaje'] = $mensaje_final;
    $_SESSION['tipo_mensaje'] = "success";

} catch (Exception $e) {

    $conn->rollback();

    $_SESSION['mensaje'] =
        "Error al procesar la salida: " .
        $e->getMessage();

    $_SESSION['tipo_mensaje'] = "danger";
}

header("Location: ../productos.php");

exit();

?>