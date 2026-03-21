<!-- MODAL AGREGAR -->

<div class="modal fade" id="modalAgregarProducto">

<div class="modal-dialog">
<div class="modal-content">

<form action="acciones/agregar_producto.php" method="POST">

<div class="modal-header">
<h5 class="modal-title">Agregar Producto</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="mb-3">
<label>Nombre Comercial</label>
<input type="text" name="nombre_comercial" class="form-control" required>
</div>

<div class="mb-3">
<label>Nombre Común</label>
<input type="text" name="nombre_comun" class="form-control">
</div>

<div class="mb-3">
<label>Categoría</label>
<input type="text" name="categoria_producto" class="form-control">
</div>

<div class="mb-3">

<label>Unidad de Medida</label>

<select name="unidad_id" class="form-control" required>

<option value="">Seleccione</option>

<?php while($u = $unidades->fetch_assoc()): ?>

<option value="<?= $u['id_unidad'] ?>">
<?= $u['nombre_unidad'] ?>
</option>

<?php endwhile; ?>

</select>

</div>

</div>

<div class="modal-footer">
<button class="btn btn-success">Guardar</button>
</div>

</form>

</div>
</div>
</div>