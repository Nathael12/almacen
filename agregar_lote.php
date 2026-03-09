<?php
    include("common/conexion.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $producto_id = $_POST['producto_id'];
        $proveedor_id = $_POST['proveedor_id'];
        $fecha_entrada = $_POST['fecha_entrada'];
        $fecha_caducidad = $_POST['fecha_caducidad'];

        $sql = "INSERT INTO lotes 
                (producto_id, proveedor_id, fecha_entrada, fecha_caducidad, estado)
                VALUES (?, ?, ?, ?, 1)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", 
            $producto_id, 
            $proveedor_id, 
            $fecha_entrada, 
            $fecha_caducidad
        );

        if ($stmt->execute()) {
            header("Location: lotes.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $productos = $conn->query("SELECT * FROM productos");
    $proveedores = $conn->query("SELECT * FROM proveedor");
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Agregar Lote</title>

<!-- Bootstrap -->
<link rel="stylesheet" href="css/bootstrap.min.css">

<!-- CSS -->
<link rel="stylesheet" href="css/editarLote.css">

</head>

<body>

<div class="container py-5 d-flex justify-content-center">

<div class="form-wrapper">

<div class="titulo-contenedor">
<h2 class="text-center mb-4">Agregar Lote</h2>
</div>

<div class="form-container">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">
<label>Producto</label>
<select name="producto_id" class="form-control" required>

<option value="">Seleccione</option>

<?php while($p = $productos->fetch_assoc()): ?>

<option value="<?= $p['id_producto'] ?>">
<?= $p['nombre_comercial'] ?>
</option>

<?php endwhile; ?>

</select>
</div>

<div class="col-md-6 mb-3">
<label>Proveedor</label>
<select name="proveedor_id" class="form-control" required>

<option value="">Seleccione</option>

<?php while($pr = $proveedores->fetch_assoc()): ?>

<option value="<?= $pr['id_proveedor'] ?>">
<?= $pr['nombre'] ?>
</option>

<?php endwhile; ?>

</select>
</div>

<div class="col-md-6 mb-3">
<label>Fecha Entrada</label>
<input type="date" name="fecha_entrada" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Fecha Caducidad</label>
<input type="date" name="fecha_caducidad" class="form-control" required>
</div>

</div>

<div class="text-center mt-3">

<button type="submit" class="btn btn-success me-2">
Guardar
</button>

<a href="lotes.php" class="btn btn-secondary">
Cancelar
</a>

</div>

</form>

</div>
</div>
</div>

</body>
</html>