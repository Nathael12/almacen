<?php
    include("common/conexion.php");

    $busqueda = "";

    $sql = "
    SELECT l.id_lote,
        l.fecha_entrada,
        l.fecha_caducidad,
        l.fecha_salida,
        l.estado,
        p.nombre_comercial,
        pr.nombre AS nombre_proveedor
    FROM lotes l
    LEFT JOIN productos p ON l.producto_id = p.id_producto
    LEFT JOIN proveedor pr ON l.proveedor_id = pr.id_proveedor
    ";

    if (!empty($_GET['q'])) {
        $busqueda = $_GET['q'];
        $sql .= " WHERE p.nombre_comercial LIKE ?
                OR pr.nombre LIKE ?";
        $sql .= " ORDER BY l.fecha_caducidad ASC";

        $stmt = $conn->prepare($sql);
        $like = "%$busqueda%";
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql .= " ORDER BY l.fecha_caducidad ASC";
        $result = $conn->query($sql);
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Lotes</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-4">

<h2 class="text-center mb-4">Control de Lotes</h2>

<form method="get" class="d-flex mb-3">
<input type="text" name="q" class="form-control me-2"
placeholder="Buscar por producto o proveedor..."
value="<?= htmlspecialchars($busqueda) ?>">
<button class="btn btn-primary">Buscar</button>
<a href="agregar_lote.php" class="btn btn-success ms-2">Agregar</a>
<a href="index.php" class="btn btn-secondary ms-2">Inicio</a>
</form>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Producto</th>
<th>Proveedor</th>
<th>Fecha Entrada</th>
<th>Fecha Caducidad</th>
<th>Fecha Salida</th>
<th>Estado</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>

<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>

<?php
$hoy = date("Y-m-d");
$alerta = "";

?>

<tr class="<?= $alerta ?>">
<td><?= $row['id_lote'] ?></td>
<td><?= $row['nombre_comercial'] ?></td>
<td><?= $row['nombre_proveedor'] ?></td>
<td><?= $row['fecha_entrada'] ?></td>
<td><?= $row['fecha_caducidad'] ?></td>
<td><?= $row['fecha_salida'] ?? '-' ?></td>

<td>
<?php if ($row['estado'] == 1): ?>
<span class="badge bg-success">Activo</span>
<?php else: ?>
<span class="badge bg-secondary">Inactivo</span>
<?php endif; ?>
</td>

<td>
<a href="editar_lote.php?id=<?= $row['id_lote'] ?>" 
class="btn btn-warning btn-sm">Editar</a>

<a href="eliminar_lote.php?id=<?= $row['id_lote'] ?>" 
class="btn btn-danger btn-sm"
onclick="return confirm('¿Eliminar lote?')">Borrar</a>
</td>
</tr>

<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="7" class="text-center">No hay lotes registrados</td>
</tr>
<?php endif; ?>

</tbody>
</table>
</div>
</body>
</html>