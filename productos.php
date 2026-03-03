<?php
    include("common/conexion.php");

    $busqueda = "";

    $sql = "
    SELECT p.id_producto,
        p.nombre_comercial,
        p.nombre_comun,
        p.categoria_producto,
        u.nombre_unidad,
        IFNULL(COUNT(l.id_lote),0) AS total_lotes
    FROM productos p
    LEFT JOIN unidad_medida u ON p.unidad_id = u.id_unidad
    LEFT JOIN lotes l ON p.id_producto = l.producto_id
    ";

    if (!empty($_GET['q'])) {
        $busqueda = $_GET['q'];
        $sql .= " WHERE p.nombre_comercial LIKE ?
                OR p.nombre_comun LIKE ?
                OR p.categoria_producto LIKE ?";
        $sql .= " GROUP BY p.id_producto";

        $stmt = $conn->prepare($sql);
        $like = "%$busqueda%";
        $stmt->bind_param("sss", $like, $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql .= " GROUP BY p.id_producto";
        $result = $conn->query($sql);
    }
?>

<!DOCTYPE html>
    <html lang="es">
    <head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/productos.css">
    </head>
    <body class="bg-light">

        <div class="container py-4">

        <div class="titulo-contenedor">
            <h2>Lista de Productos</h2>
        </div>

        <form method="get" class="d-flex mb-3">
        <input type="text" name="q" class="form-control me-2"
        placeholder="Buscar producto..."
        value="<?= htmlspecialchars($busqueda) ?>">
        <button class="btn btn-primary">Buscar</button>
        <a href="agregar_producto.php" class="btn btn-success ms-2">Agregar</a>
        <a href="index.php" class="btn btn-secondary ms-2">Inicio</a>
        </form>

        <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
        <th>ID</th>
        <th>Nombre Comercial</th>
        <th>Nombre Común</th>
        <th>Unidad</th>
        <th>Categoría</th>
        <th>Total Lotes</th>
        <th>Acciones</th>
        </tr>
        </thead>
        <tbody>

        <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
        <td><?= $row['id_producto'] ?></td>
        <td><?= $row['nombre_comercial'] ?></td>
        <td><?= $row['nombre_comun'] ?></td>
        <td><?= $row['nombre_unidad'] ?? '-' ?></td>
        <td><?= $row['categoria_producto'] ?></td>
        <td>
        <span class="badge text-dark">
        <?= $row['total_lotes'] ?>
        </span>
        </td>
        <td>
            
        <a href="editar_producto.php?id=<?= $row['id_producto'] ?>" 
        class="btn btn-warning btn-sm">Editar</a>

        <a href="eliminar_producto.php?id=<?= $row['id_producto'] ?>" 
        class="btn btn-danger btn-sm"
        onclick="return confirm('¿Eliminar producto?')">Borrar</a>

        <a href="editar_producto.php?id=<?= $row['id_producto'] ?>" 
        class="btn btn-success btn-sm">Usar</a>
        </td>
        </tr>

        <?php endwhile; ?>
        <?php else: ?>
        <tr>
        <td colspan="7" class="text-center">No hay productos registrados</td>
        </tr>
        <?php endif; ?>

        </tbody>
        </table>

        </div>
    </body>
</html>