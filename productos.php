<?php
session_start();
$logged = $_SESSION['LOGGED'] ?? 0;

if ($logged == 0) {
    //header('Location: index.php');
}

include("common/conexion.php");

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'info';
    unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
}

$busqueda = "";

$sql = "
SELECT p.id_producto, p.nombre_comercial, p.nombre_comun, p.categoria_producto,
       p.unidad_id, u.nombre_unidad, IFNULL(COUNT(l.id_lote), 0) AS total_lotes
FROM productos p
LEFT JOIN unidad_medida u ON p.unidad_id = u.id_unidad
LEFT JOIN lotes l ON p.id_producto = l.producto_id
";

if (!empty($_GET['q'])) {
    $busqueda = $_GET['q'];
    $sql .= " WHERE p.nombre_comercial LIKE ? OR p.nombre_comun LIKE ? OR p.categoria_producto LIKE ?";
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

$unidades = $conn->query("SELECT * FROM unidad_medida");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/registro.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <div class="container py-4">

        <?php if (isset($mensaje)): ?>
        <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show mb-4" role="alert">
            <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="titulo-contenedor">
            <h2>Lista de Productos</h2>
            <br>
        </div>

        <form method="get" class="d-flex mb-3">
            <input type="text" name="q" class="form-control me-2" 
                   placeholder="Buscar producto..." 
                   value="<?= htmlspecialchars($busqueda) ?>">
            <button class="btn btn-primary">Buscar</button>
            <button type="button" class="btn btn-success ms-2" 
                    data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
                Agregar
            </button>
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
                        <td><?= htmlspecialchars($row['nombre_comercial']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_comun']) ?></td>
                        <td><?= $row['nombre_unidad'] ?? '-' ?></td>
                        <td><?= htmlspecialchars($row['categoria_producto']) ?></td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <?= $row['total_lotes'] ?>
                            </span>
                        </td>
                        <td> 
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-warning btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditar<?= $row['id_producto'] ?>">
                                    Editar
                                </button>
                                <a class="btn btn-danger btn-sm">Borrar</a>
                                <a class="btn btn-success btn-sm">Usar</a>
                                <a href="registro.php?id_producto=<?= $row['id_producto'] ?>" 
                                   class="btn btn-info btn-sm">Lotes</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-white">No hay productos registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

    <?php include_once('modals/modal_agregar_producto.php'); ?>
    <?php include_once('modals/modal_editar_productos.php'); ?>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>