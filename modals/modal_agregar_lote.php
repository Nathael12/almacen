<div class="modal fade" id="modalAgregarLote" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
           <form action="acciones/agregar_lote.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Lote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <select name="producto_id" class="form-control" required>
                            <option value="">Seleccione</option>
                            <?php 
                            // Reutiliza de forma limpia la variable global de lotes.php
                            while($p = $productos->fetch_assoc()): 
                            ?>
                                <option value="<?= $p['id_producto'] ?>">
                                    <?= htmlspecialchars($p['nombre_comercial']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Proveedor</label>
                        <select name="proveedor_id" class="form-control" required>
                            <option value="">Seleccione</option>
                            <?php 
                            // Reutiliza de forma limpia la variable global de lotes.php
                            while($pr = $proveedores->fetch_assoc()): 
                            ?>
                                <option value="<?= $pr['id_proveedor'] ?>">
                                    <?= htmlspecialchars($pr['nombre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha Entrada</label>
                        <input type="date" name="fecha_entrada" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha Caducidad</label>
                        <input type="date" name="fecha_caducidad" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" min="1" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Lote</button>
                </div>
            </form>
        </div>
    </div>
</div>