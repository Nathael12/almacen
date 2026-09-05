<?php

session_start();

include("../common/conexion.php");


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['mensaje'] = "Solicitud no válida.";
    $_SESSION['tipo_mensaje'] = "danger";

    header("Location: ../productos.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| VALIDAR PRODUCTO
|--------------------------------------------------------------------------
*/

$id_producto = isset($_POST['id_producto'])
    ? intval($_POST['id_producto'])
    : 0;


/*
|--------------------------------------------------------------------------
| VALIDAR LOTE
|--------------------------------------------------------------------------
*/

$id_lote = isset($_POST['id_lote'])
    ? intval($_POST['id_lote'])
    : 0;


/*
|--------------------------------------------------------------------------
| VALIDAR CANTIDAD
|--------------------------------------------------------------------------
*/

$cantidad_usada = isset($_POST['cantidad_usada'])
    ? intval($_POST['cantidad_usada'])
    : 0;


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


$fecha_actual = date('Y-m-d');


$conn->begin_transaction();


try {


    /*
    |--------------------------------------------------------------------------
    | 1. OBTENER EL LOTE Y BLOQUEARLO
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | VALIDAR ESTADO
    |--------------------------------------------------------------------------
    */

    if (intval($lote['estado']) !== 1) {

        throw new Exception(
            "El lote seleccionado ya no está disponible."
        );

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR CANTIDAD DISPONIBLE
    |--------------------------------------------------------------------------
    */

    $cantidad_disponible = intval(
        $lote['cantidad']
    );


    if ($cantidad_usada > $cantidad_disponible) {

        throw new Exception(
            "No puedes utilizar $cantidad_usada unidades. " .
            "Solo hay $cantidad_disponible unidades disponibles."
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CALCULAR NUEVA CANTIDAD
    |--------------------------------------------------------------------------
    */

    $nueva_cantidad =
        $cantidad_disponible -
        $cantidad_usada;


    /*
    |--------------------------------------------------------------------------
    | 2. REGISTRAR LA SALIDA
    |--------------------------------------------------------------------------
    */

    $sql_salida = "

        INSERT INTO salidas
        (
            producto_id,
            cantidad_usada,
            fecha_salida
        )

        VALUES (?, ?, ?)

    ";


    $stmt_salida =
        $conn->prepare($sql_salida);


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


    /*
    |--------------------------------------------------------------------------
    | 3. ACTUALIZAR CANTIDAD DEL LOTE
    |--------------------------------------------------------------------------
    */

    if ($nueva_cantidad > 0) {


        /*
        |--------------------------------------------------------------
        | AÚN QUEDAN PRODUCTOS
        |--------------------------------------------------------------
        */

        $sql_actualizar = "

            UPDATE lotes

            SET cantidad = ?

            WHERE id_lote = ?
            AND estado = 1

        ";


        $stmt_actualizar =
            $conn->prepare($sql_actualizar);


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


        /*
        |--------------------------------------------------------------
        | YA NO QUEDA NADA
        |--------------------------------------------------------------
        */

        $sql_cerrar = "

            UPDATE lotes

            SET
                cantidad = 0,
                estado = 0,
                fecha_salida = ?

            WHERE id_lote = ?
            AND estado = 1

        ";


        $stmt_cerrar =
            $conn->prepare($sql_cerrar);


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


    /*
    |--------------------------------------------------------------------------
    | CONFIRMAR TRANSACCIÓN
    |--------------------------------------------------------------------------
    */

    $conn->commit();


    $_SESSION['mensaje'] =
        $mensaje_final;


    $_SESSION['tipo_mensaje'] =
        "success";


} catch (Exception $e) {


    $conn->rollback();


    $_SESSION['mensaje'] =
        "Error al procesar la salida: " .
        $e->getMessage();


    $_SESSION['tipo_mensaje'] =
        "danger";

}


header("Location: ../productos.php");

exit();

?>