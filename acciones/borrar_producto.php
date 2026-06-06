<?php
session_start();
include("../common/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_producto'])) {
    $id_producto = intval($_POST['id_producto']);

    // Borrado lógico: Cambia el estado a 0 (Inactivo)
    $sql = "UPDATE productos SET estado = 0 WHERE id_producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_producto);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "El producto ha sido deshabilitado correctamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al intentar actualizar el producto.";
        $_SESSION['tipo_mensaje'] = "danger";
    }
} else {
    $_SESSION['mensaje'] = "Petición inválida.";
    $_SESSION['tipo_mensaje'] = "danger";
}

// Redirecciona de vuelta a tu archivo principal
header("Location: ../productos.php");
exit();