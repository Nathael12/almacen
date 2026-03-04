<?php
    include("common/conexion.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nombre_comercial = $_POST['nombre_comercial'];
        $nombre_comun = $_POST['nombre_comun'];
        $categoria = $_POST['categoria_producto'];
        $unidad_id = $_POST['unidad_id'];

        $sql = "INSERT INTO productos 
                (nombre_comercial, nombre_comun, categoria_producto, unidad_id)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nombre_comercial, $nombre_comun, $categoria, $unidad_id);

        if ($stmt->execute()) {
            header("Location: productos.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }
    $unidades = $conn->query("SELECT * FROM unidad_medida");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Agregar Producto</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container py-4">

<div class="titulo-contenedor">
    <h2>Agregar Producto</h2>
</div>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">
<label>Nombre Comercial</label>
<input type="text" name="nombre_comercial" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Nombre Común</label>
<input type="text" name="nombre_comun" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Categoría</label>
<input type="text" name="categoria_producto" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Unidad de Medida</label>
<select name="unidad_id" class="form-control" required>
<option value="">Seleccione</option>

<?php while($u = $unidades->fetch_assoc()): ?>
<option value="<?= $u['id_unidad'] ?>">
<?= $u['nombre_unidad'] ?>
</option>
<?php endwhile; ?>

</select>
</div>

</div>

<button type="submit" class="btn btn-success">Guardar</button>
<a href="productos.php" class="btn btn-secondary">Cancelar</a>

</form>
</div>
</body>
</html>