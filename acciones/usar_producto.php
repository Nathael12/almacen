<?php
session_start();
include("../common/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_lote'])) {
    $id_lote = intval($_POST['id_lote']);
    $id_producto = intval($_POST['id_producto']);
    $cantidad_usada = intval($_POST['cantidad']);

    // 1. Validar existencias del lote activo
    $sql = "SELECT cantidad FROM lotes WHERE id_lote = ? AND estado = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_lote);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($lote = $result->fetch_assoc()) {
        $stock_actual = $lote['cantidad'];

        if ($cantidad_usada > $stock_actual) {
            $_SESSION['mensaje'] = "Error: La cantidad solicitada supera las existencias del lote.";
            $_SESSION['tipo_mensaje'] = "danger";
        } else {
            $nuevo_stock = $stock_actual - $cantidad_usada;

            // 2. Actualizar la cantidad restante en el lote
            $update = $conn->prepare("UPDATE lotes SET cantidad = ? WHERE id_lote = ?");
            $update->bind_param("ii", $nuevo_stock, $id_lote);
            $update->execute();

            // Si el lote se vacía, lo desactivamos y guardamos su fecha de cierre
            if ($nuevo_stock == 0) {
                $cerrar = $conn->prepare("UPDATE lotes SET estado = 0, fecha_salida = CURDATE() WHERE id_lote = ?");
                $cerrar->bind_param("i", $id_lote);
                $cerrar->execute();
            }

            // 3. REGISTRO EN LA TABLA SALIDAS: Esencial para el reporte de frecuencias
            // Se usa CURDATE() directo en SQL para evitar desfases de horario con PHP
            $historial = $conn->prepare("INSERT INTO salidas (producto_id, lote_id, cantidad_usada, fecha_salida) VALUES (?, ?, ?, CURDATE())");
            $historial->bind_param("iii", $id_producto, $id_lote, $cantidad_usada);
            $historial->execute();

            $_SESSION['mensaje'] = "Se descontaron exitosamente $cantidad_usada unidades.";
            $_SESSION['tipo_mensaje'] = "success";
        }
    } else {
        $_SESSION['mensaje'] = "El lote seleccionado no existe o está inactivo.";
        $_SESSION['tipo_mensaje'] = "warning";
    }
} else {
    $_SESSION['mensaje'] = "Datos de formulario incompletos.";
    $_SESSION['tipo_mensaje'] = "danger";
}

header("Location: ../productos.php");
exit();