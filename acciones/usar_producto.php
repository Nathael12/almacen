<?php
session_start();
include("../common/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_producto']) && !empty($_POST['id_lotes'])) {
    
    $id_producto = intval($_POST['id_producto']);
    $id_lotes = array_map('intval', $_POST['id_lotes']); // Recibe el arreglo de IDs de lotes
    $cantidad_usada = count($id_lotes);
    $fecha_actual = date('Y-m-d');

    if ($cantidad_usada <= 0) {
        $_SESSION['mensaje'] = "Debe seleccionar al menos un lote.";
        $_SESSION['tipo_mensaje'] = "warning";
        header("Location: ../productos.php");
        exit();
    }

    $conn->begin_transaction();

    try {
        // 1. Insertar la salida general (o total de unidades usadas) en la tabla SALIDAS
        $insert_salida = $conn->prepare("INSERT INTO salidas (producto_id, cantidad_usada, fecha_salida) VALUES (?, ?, ?)");
        $insert_salida->bind_param("iis", $id_producto, $cantidad_usada, $fecha_actual);
        $insert_salida->execute();

        // 2. Desactivar todos los lotes seleccionados uno a uno
        $cerrar_lote = $conn->prepare("UPDATE lotes SET estado = 0, fecha_salida = ? WHERE id_lote = ? AND estado = 1");

        foreach ($id_lotes as $id_lote) {
            $cerrar_lote->bind_param("si", $fecha_actual, $id_lote);
            $cerrar_lote->execute();
        }

        $conn->commit();

        $_SESSION['mensaje'] = "Salida de $cantidad_usada lote(s) registrada exitosamente.";
        $_SESSION['tipo_mensaje'] = "success";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['mensaje'] = "Error al procesar la salida: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = "danger";
    }

} else {
    $_SESSION['mensaje'] = "Por favor completa todos los campos del formulario.";
    $_SESSION['tipo_mensaje'] = "danger";
}

header("Location: ../productos.php");
exit();
?>