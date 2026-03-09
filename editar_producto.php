<?php
    include("common/conexion.php");

    if (!isset($_GET['id'])) {
        header("Location: productos.php");
        exit;
    }

    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM productos WHERE id_producto = $id");
    $row = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nombre_comercial = $_POST['nombre_comercial'];
        $nombre_comun = $_POST['nombre_comun'];
        $categoria = $_POST['categoria_producto'];
        $unidad_id = $_POST['unidad_id'];

        $sql = "UPDATE productos SET 
                nombre_comercial='$nombre_comercial',
                nombre_comun='$nombre_comun',
                categoria_producto='$categoria',
                unidad_id=$unidad_id
                WHERE id_producto=$id";

        if ($conn->query($sql)) {
            header("Location: productos.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Obtener unidades para el select
    $unidades = $conn->query("SELECT * FROM unidad_medida");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Producto</title>

<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/editarProducto.css">

</head>

<body>

<div class="container py-5">

<h2 class="text-center mb-4">Editar Producto</h2>

<div class="form-container">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">
<label>Nombre Comercial</label>
<input type="text" name="nombre_comercial"
class="form-control"
value="<?= $row['nombre_comercial'] ?>" required>
</div>

<div class="col-md-6 mb-3">
<label>Nombre Común</label>
<input type="text" name="nombre_comun"
class="form-control"
value="<?= $row['nombre_comun'] ?>">
</div>

<div class="col-md-6 mb-3">
<label>Categoría</label>
<input type="text" name="categoria_producto"
class="form-control"
value="<?= $row['categoria_producto'] ?>">
</div>

<div class="col-md-6 mb-3">
<label>Unidad de Medida</label>

<select name="unidad_id" class="form-control" required>

<?php while($u = $unidades->fetch_assoc()): ?>

<option value="<?= $u['id_unidad'] ?>"
<?= ($u['id_unidad'] == $row['unidad_id']) ? 'selected' : '' ?>>

<?= $u['nombre_unidad'] ?>

</option>

<?php endwhile; ?>

</select>

</div>

</div>

<div class="text-center mt-3">

<button type="submit" class="btn btn-warning me-2">
Actualizar
</button>

<a href="productos.php" class="btn btn-secondary">
Cancelar
</a>

</div>

</form>

</div>

</div>

</body>
</html>