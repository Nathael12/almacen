<?php
$sql_modal = "SELECT l.*, p.nombre_comercial, pr.nombre AS nombre_proveedor 
              FROM lotes l 
              LEFT JOIN productos p ON l.producto_id = p.id_producto 
              LEFT JOIN proveedor pr ON l.proveedor_id = pr.id_proveedor 
              ORDER BY l.fecha_caducidad ASC";

if (!empty($_GET['q'])) {
    $busqueda = $_GET['q'];
    $sql_modal .= " WHERE p.nombre_comercial LIKE '%$busqueda%' OR pr.nombre LIKE '%$busqueda%'";
}

$result_modal = $conn->query($sql_modal);

while ($row = $result_modal->fetch_assoc()): 
?>
<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar<?= $row['id_lote'] ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../acciones/editar_lote.php?id=<?= $row['id_lote'] ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Lote #<?= $row['id_lote'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Producto</label>
                        <select name="producto_id" class="form-control" required>
                            <?php 
                            $productos_modal = $conn->query("SELECT * FROM productos");
                            while($p = $productos_modal->fetch_assoc()): 
                            ?>
                            <option value="<?= $p['id_producto'] ?>" 
                                    <?= $p['id_producto'] == $row['producto_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nombre_comercial']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Proveedor</label>
                        <select name="proveedor_id" class="form-control" required>
                            <?php 
                            $proveedores_modal = $conn->query("SELECT * FROM proveedor");
                            while($pr = $proveedores_modal->fetch_assoc()): 
                            ?>
                            <option value="<?= $pr['id_proveedor'] ?>" 
                                    <?= $pr['id_proveedor'] == $row['proveedor_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pr['nombre']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Fecha Entrada</label>
                        <input type="date" name="fecha_entrada" class="form-control" 
                               value="<?= $row['fecha_entrada'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Fecha Caducidad</label>
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
<?php endwhile; ?>