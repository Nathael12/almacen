<?php
session_start();
include("../common/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {

    // Sanitizar y castear datos
    $id = intval($_GET['id']);
    $producto_id = intval($_POST['producto_id']);
    $proveedor_id = intval($_POST['proveedor_id']);
    $fecha_entrada = trim($_POST['fecha_entrada']);
    $fecha_caducidad = trim($_POST['fecha_caducidad']);

    if ($id > 0 && $producto_id > 0 && $proveedor_id > 0 && !empty($fecha_entrada) && !empty($fecha_caducidad)) {
        
        $sql = "UPDATE lotes SET
                producto_id = ?,
                proveedor_id = ?,
                fecha_entrada = ?,
                fecha_caducidad = ?
                WHERE id_lote = ?";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iissi", $producto_id, $proveedor_id, $fecha_entrada, $fecha_caducidad, $id);

            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Lote actualizado correctamente.";
                $_SESSION['tipo_mensaje'] = "success";
            } else {
                $_SESSION['mensaje'] = "Error al actualizar en la base de datos: " . $stmt->error;
                $_SESSION['tipo_mensaje'] = "danger";
            }

            $stmt->close();
        } else {
            $_SESSION['mensaje'] = "Error en la preparaci贸n de la consulta: " . $conn->error;
            $_SESSION['tipo_mensaje'] = "danger";
        }

    } else {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        $_SESSION['tipo_mensaje'] = "warning";
    }
}
header("Location: ../lotes.php");
exit();
?>