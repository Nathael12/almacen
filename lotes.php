<?php
session_start(); // Esencial para que funcionen los mensajes de éxito/error en las alertas
include("common/conexion.php");

$busqueda = "";

// Consulta SQL modificada: Se añade "WHERE l.estado = 1" para que lo inactivado no se muestre en pantalla
$sql = "
SELECT l.id_lote, l.producto_id, l.proveedor_id, l.cantidad,
       l.fecha_entrada, l.fecha_caducidad, l.fecha_salida, l.estado,
       p.nombre_comercial, pr.nombre AS nombre_proveedor
FROM lotes l
LEFT JOIN productos p ON l.producto_id = p.id_producto
LEFT JOIN proveedor pr ON l.proveedor_id = pr.id_proveedor
WHERE l.estado = 1
";

if (!empty($_GET['q'])) {
    $busqueda = $_GET['q'];
    // Encadenamos la búsqueda con un AND para respetar que el lote deba estar activo
    $sql .= " AND (p.nombre_comercial LIKE ? OR pr.nombre LIKE ?)";
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
    <link rel="stylesheet" href="css/productos.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <div class="container py-4">
        
        <div class="titulo-contenedor">
            <h2>Control de Lotes</h2>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['mensaje'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['mensaje']); unset($_SESSION['tipo_mensaje']); ?>
        <?php endif; ?>

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

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Proveedor</th>
                    <th>Cantidad</th>
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
                        <td><?= htmlspecialchars($row['id_lote']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_comercial']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_proveedor']) ?></td>
                        <td>
                            <span class="badge bg-primary">
                                <?= $row['cantidad'] ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($row['fecha_entrada'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['fecha_caducidad'])) ?></td>
                        <td>
                            <?= $row['fecha_salida']
                                ? date('d/m/Y', strtotime($row['fecha_salida']))
                                : '-' ?>
                        </td>
                        <td>
                            <?php if ($row['estado'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-warning btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditar<?= $row['id_lote'] ?>">
                                    Editar
                                </button>
                                
                                <button type="button" 
                                        class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalInactivarLote"
                                        data-id="<?= $row['id_lote'] ?>"
                                        data-nombre="<?= htmlspecialchars($row['nombre_comercial']) ?>">
                                    Borrar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-3">
                            No se encontraron lotes activos registrados.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include_once('modals/modal_agregar_lote.php'); ?>
    <?php include_once('modals/modal_editar_lote.php'); ?>

    <div class="modal fade" id="modalInactivarLote" tabindex="-1" aria-labelledby="modalInactivarLoteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalInactivarLoteLabel">¿Dar de baja este lote?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="acciones/borrar_lote.php" method="POST">
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas marcar como **terminado/inactivo** el lote del producto: <strong id="nombre_producto_modal"></strong>?</p>
                        <p class="text-muted small">Esta acción no eliminará físicamente el registro del sistema, manteniendo intacta la consistencia de los reportes mensuales previos.</p>
                        
                        <input type="hidden" name="id_lote" id="id_lote_modal">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Confirmar Baja</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>

    <script>
    const modalInactivar = document.getElementById('modalInactivarLote');
    if (modalInactivar) {
        modalInactivar.addEventListener('show.bs.modal', event => {
            const boton = event.relatedTarget;
            
            const idLote = boton.getAttribute('data-id');
            const nombreProducto = boton.getAttribute('data-nombre');
            
            modalInactivar.querySelector('#id_lote_modal').value = idLote;
            modalInactivar.querySelector('#nombre_producto_modal').textContent = nombreProducto;
        });
    }
    </script>
</body>
</html>