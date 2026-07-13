<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include("common/conexion.php");

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'info';
    unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
}

$busqueda = "";

// Consulta SQL modificada: FILTRA con "WHERE p.estado = 1" para ocultar de la pantalla lo borrado lógicamente
$sql = "
SELECT p.id_producto, p.nombre_comercial, p.nombre_comun, p.categoria_producto, p.estado,
       p.unidad_id, u.nombre_unidad, IFNULL(COUNT(l.id_lote), 0) AS total_lotes
FROM productos p
LEFT JOIN unidad_medida u ON p.unidad_id = u.id_unidad
LEFT JOIN lotes l ON p.id_producto = l.producto_id
WHERE p.estado = 1
";

if (!empty($_GET['q'])) {
    $busqueda = $_GET['q'];
    // Encadenamos los LIKE con un AND para mantener el filtro de estado activo
    $sql .= " AND (p.nombre_comercial LIKE ? OR p.nombre_comun LIKE ? OR p.categoria_producto LIKE ?)";
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

                                <button type="button" 
                                        class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalInactivarProducto"
                                        data-id="<?= $row['id_producto'] ?>"
                                        data-nombre="<?= htmlspecialchars($row['nombre_comercial'], ENT_QUOTES) ?>">
                                    Borrar
                                </button>

                                <button type="button"
                                        class="btn btn-success btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalUsarProducto"
                                        onclick="cargarProducto(
                                            <?= $row['id_producto'] ?>,
                                            '<?= htmlspecialchars($row['nombre_comercial'], ENT_QUOTES) ?>'
                                        )">
                                    Usar
                                </button>

                                <a href="registro.php?id_producto=<?= $row['id_producto'] ?>"
                                   class="btn btn-info btn-sm">
                                    Lotes
                                </a>

                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">No hay productos registrados activos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include_once('modals/modal_agregar_producto.php'); ?>
    <?php include_once('modals/modal_editar_productos.php'); ?>
    <?php include_once('modals/modal_usar_producto.php'); ?>
    <?php include_once('modals/modal_borrar_producto.php'); ?> 

    <script src="js/bootstrap.bundle.min.js"></script>

    <script>
    const modalInactivarProd = document.getElementById('modalInactivarProducto');
    if (modalInactivarProd) {
        modalInactivarProd.addEventListener('show.bs.modal', event => {
            const boton = event.relatedTarget;
            
            const idProducto = boton.getAttribute('data-id');
            const nombreProducto = boton.getAttribute('data-nombre');
            
            modalInactivarProd.querySelector('#id_producto_modal').value = idProducto;
            modalInactivarProd.querySelector('#nombre_producto_modal').textContent = nombreProducto;
        });
    }
    </script>
</body>
</html>