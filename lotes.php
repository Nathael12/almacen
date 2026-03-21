<?php
include("common/conexion.php");

$busqueda = "";

$sql = "
SELECT l.id_lote, l.producto_id, l.proveedor_id, l.fecha_entrada,
       l.fecha_caducidad, l.fecha_salida, l.estado,
       p.nombre_comercial, pr.nombre AS nombre_proveedor
FROM lotes l
LEFT JOIN productos p ON l.producto_id = p.id_producto
LEFT JOIN proveedor pr ON l.proveedor_id = pr.id_proveedor
";

if (!empty($_GET['q'])) {
    $busqueda = $_GET['q'];
    $sql .= " WHERE p.nombre_comercial LIKE ? OR pr.nombre LIKE ?";
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

$productos = $conn->query("SELECT * FROM productos");
$proveedores = $conn->query("SELECT * FROM proveedor");

// Reset punteros para modales
$productos->data_seek(0);
$proveedores->data_seek(0);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lotes</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/productos.css"> <!-- ✅ CAMBIO: productos.css -->
</head>
<body>
    <?php include("navbar.php"); ?>

    <div class="container py-4">
        
        <!-- ✅ AGREGADO: titulo-contenedor -->
        <div class="titulo-contenedor">
            <h2>Control de Lotes</h2>
        </div>

        <!-- ✅ Tu formulario exacto -->
        <form method="get" class="d-flex mb-3">
            <input type="text" name="q" class="form-control me-2" 
                   placeholder="Buscar por producto o proveedor..." 
                   value="<?= htmlspecialchars($busqueda) ?>">
            <button class="btn btn-primary">Buscar</button>
            <button type="button" class="btn btn-success ms-2" 
                    data-bs-toggle="modal" data-bs-target="#modalAgregarLote">
                Agregar
            </button>
        </form>

        <!-- ✅ Tu tabla exacta -->
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
                    <tr>
                        <td><?= $row['id_lote'] ?></td>
                        <td><?= htmlspecialchars($row['nombre_comercial']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_proveedor']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['fecha_entrada'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['fecha_caducidad'])) ?></td>
                        <td><?= $row['fecha_salida'] ? date('d/m/Y', strtotime($row['fecha_salida'])) : '-' ?></td>
                        <td>
                            <?php if ($row['estado'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <!-- ✅ td:last-child para CSS -->
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-warning btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditar<?= $row['id_lote'] ?>">
                                    Editar
                                </button>
                                <a class="btn btn-danger btn-sm">Borrar</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-white">No hay lotes registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ✅ Tus modales exactos -->
    <?php include_once('modals/modal_agregar_lote.php'); ?>
    <?php include_once('modals/modal_editar_lote.php'); ?>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>