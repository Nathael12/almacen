<?php
// modals/modal_editar_productos.php
// Recargar los datos para los modales
$sql_modal = "
SELECT p.id_producto,
       p.nombre_comercial,
       p.nombre_comun,
       p.categoria_producto,
       p.unidad_id
FROM productos p
";

if (!empty($_GET['q'])) {
    $busqueda_modal = $_GET['q'];
    $sql_modal .= " WHERE p.nombre_comercial LIKE '%$busqueda_modal%'
                   OR p.nombre_comun LIKE '%$busqueda_modal%'
                   OR p.categoria_producto LIKE '%$busqueda_modal%'";
}

$result_modal = $conn->query($sql_modal);

while ($row_modal = $result_modal->fetch_assoc()): 
?>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar<?= $row_modal['id_producto'] ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="acciones/editar_producto.php?id=<?= $row_modal['id_producto'] ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre Comercial</label>
                        <input type="text" name="nombre_comercial" class="form-control" 
                               value="<?= htmlspecialchars($row_modal['nombre_comercial']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Nombre Común</label>
                        <input type="text" name="nombre_comun" class="form-control" 
                               value="<?= htmlspecialchars($row_modal['nombre_comun']) ?>">
                    </div>
                    <div class="mb-3">
                        <label>Categoría</label>
                        <input type="text" name="categoria_producto" class="form-control" 
                               value="<?= htmlspecialchars($row_modal['categoria_producto']) ?>">
                    </div>
                    <div class="mb-3">
                        <label>Unidad de Medida</label>
                        <select name="unidad_id" class="form-control" required>
                            <option value="">Seleccione</option>
                            <?php
                            $unidadesEditar = $conn->query("SELECT * FROM unidad_medida");
                            while($u = $unidadesEditar->fetch_assoc()):
                            ?>
                            <option value="<?= $u['id_unidad'] ?>" 
                                    <?= $u['id_unidad'] == $row_modal['unidad_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['nombre_unidad']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
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