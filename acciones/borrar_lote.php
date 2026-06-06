<?php
session_start();
include("../common/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_lote'])) {
    $id_lote = intval($_POST['id_lote']);

    // UPDATE en lugar de DELETE: Cambia estado a 0 y sella la fecha de término hoy
    $sql = "UPDATE lotes SET estado = 0, fecha_salida = CURDATE() WHERE id_lote = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_lote);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['mensaje'] = "El lote ha sido marcado como terminado e inactivo correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "El lote ya estaba inactivo o no se encontró.";
            $_SESSION['tipo_mensaje'] = "info";
        }
    } else {
        $_SESSION['mensaje'] = "Error al intentar actualizar el estado del lote.";
        $_SESSION['tipo_mensaje'] = "danger";
    }
} else {
    $_SESSION['mensaje'] = "Petición inválida.";
    $_SESSION['tipo_mensaje'] = "danger";
}

// Redireccionar de vuelta a tu panel de control de productos/lotes
header("Location: ../lotes.php");
exit();
