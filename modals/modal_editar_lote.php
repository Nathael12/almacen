<?php
// 1. Cargamos las listas de productos y proveedores una sola vez fuera del bucle
$lista_productos = [];
$res_prod = $conn->query("SELECT id_producto, nombre_comercial FROM productos WHERE estado = 1");
if ($res_prod) {
    while ($p = $res_prod->fetch_assoc()) {
        $lista_productos[] = $p;
    }
}

$lista_proveedores = [];
$res_prov = $conn->query("SELECT id_proveedor, nombre FROM proveedor");
if ($res_prov) {
    while ($pr = $res_prov->fetch_assoc()) {
        $lista_proveedores[] = $pr;
    }
}

// 2. Armamos la consulta del modal colocando el WHERE ANTES del ORDER BY
$where_modal = "";
if (!empty($_GET['q'])) {
    $busqueda_modal = $conn->real_escape_string($_GET['q']);
    $where_modal = " WHERE p.nombre_comercial LIKE '%$busqueda_modal%' OR pr.nombre LIKE '%$busqueda_modal%' ";
}

$sql_modal = "SELECT l.*, p.nombre_comercial, pr.nombre AS nombre_proveedor 
              FROM lotes l 
              LEFT JOIN productos p ON l.producto_id = p.id_producto 
              LEFT JOIN proveedor pr ON l.proveedor_id = pr.id_proveedor 
              $where_modal
              ORDER BY l.fecha_caducidad ASC";

$result_modal = $conn->query($sql_modal);

if ($result_modal && $result_modal->num_rows > 0):
    while ($row = $result_modal->fetch_assoc()): 
?>
<!-- MODAL EDITAR LOTE #<?= $row['id_lote'] ?> -->
<div class="modal fade" id="modalEditar<?= $row['id_lote'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="acciones/editar_lote.php?id=<?= $row['id_lote'] ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Lote #<?= $row['id_lote'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <select name="producto_id" class="form-control" required>
                            <?php foreach ($lista_productos as $p): ?>
                            <option value="<?= $p['id_producto'] ?>" 
                                    <?= $p['id_producto'] == $row['producto_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nombre_comercial']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proveedor</label>
                        <select name="proveedor_id" class="form-control" required>
                            <?php foreach ($lista_proveedores as $pr): ?>
                            <option value="<?= $pr['id_proveedor'] ?>" 
                                    <?= $pr['id_proveedor'] == $row['proveedor_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pr['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha Entrada</label>
                        <input type="date" name="fecha_entrada" class="form-control" 
                               value="<?= $row['fecha_entrada'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha Caducidad</label>
                        <input type="date" name="fecha_caducidad" class="form-control" 
                               value="<?= $row['fecha_caducidad'] ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
    endwhile; 
endif; 
?>