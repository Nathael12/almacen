<?php
    include("common/conexion.php");

    if (!isset($_GET['id'])) {
        header("Location: lotes.php");
        exit;
    }

    $id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $producto_id = $_POST['producto_id'];
        $proveedor_id = $_POST['proveedor_id'];
        $fecha_entrada = $_POST['fecha_entrada'];
        $fecha_caducidad = $_POST['fecha_caducidad'];

        $sql = "UPDATE lotes 
                SET producto_id = ?, 
                    proveedor_id = ?, 
                    fecha_entrada = ?, 
                    fecha_caducidad = ?
                WHERE id_lote = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissi", 
            $producto_id, 
            $proveedor_id, 
            $fecha_entrada, 
            $fecha_caducidad,
            $id
        );

        if ($stmt->execute()) {
            header("Location: lotes.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $sql = "SELECT * FROM lotes WHERE id_lote = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $lote = $result->fetch_assoc();

    $productos = $conn->query("SELECT * FROM productos");
    $proveedores = $conn->query("SELECT * FROM proveedor");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Lote</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container py-4">

<div class="titulo-contenedor">
    <h2>Editar Lote</h2>
</div>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">
<label>Producto</label>
<select name="producto_id" class="form-control" required>
<?php while($p = $productos->fetch_assoc()): ?>
<option value="<?= $p['id_producto'] ?>"
<?= ($p['id_producto'] == $lote['producto_id']) ? 'selected' : '' ?>>
<?= $p['nombre_comercial'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-6 mb-3">
<label>Proveedor</label>
<select name="proveedor_id" class="form-control" required>
<?php while($pr = $proveedores->fetch_assoc()): ?>
<option value="<?= $pr['id_proveedor'] ?>"
<?= ($pr['id_proveedor'] == $lote['proveedor_id']) ? 'selected' : '' ?>>
<?= $pr['nombre'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-6 mb-3">
<label>Fecha Entrada</label>
<input type="date" name="fecha_entrada" 
class="form-control"
value="<?= $lote['fecha_entrada'] ?>" required>
</div>

<div class="col-md-6 mb-3">
<label>Fecha Caducidad</label>
<input type="date" name="fecha_caducidad" 
class="form-control"
value="<?= $lote['fecha_caducidad'] ?>" required>
</div>

</div>

<button type="submit" class="btn btn-warning">Actualizar</button>
<a href="lotes.php" class="btn btn-secondary">Cancelar</a>

</form>
</div>
</body>
</html>