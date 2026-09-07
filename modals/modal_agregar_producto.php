<!-- MODAL AGREGAR -->

<div class="modal fade" id="modalAgregarProducto" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form action="acciones/agregar_producto.php" method="POST">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Agregar Producto
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <!-- NOMBRE COMERCIAL -->

                    <div class="mb-3">

                        <label class="form-label">
                            Nombre Comercial
                        </label>

                        <input type="text"
                               name="nombre_comercial"
                               class="form-control"
                               placeholder="Ej. Detergente Ariel Doble Poder"
                               required>

                    </div>


                    <!-- NOMBRE COMÚN -->

                    <div class="mb-3">

                        <label class="form-label">
                            Nombre Común
                        </label>

                        <input type="text"
                               name="nombre_comun"
                               class="form-control"
                               placeholder="Ej. Jabón de ropa">

                    </div>


                    <!-- PRESENTACIÓN -->

                    <div class="mb-3">

                        <label class="form-label">
                            Presentación / Contenido
                        </label>

                        <input type="number"
                               name="presentacion"
                               class="form-control"
                               step="0.01"
                               min="0"
                               placeholder="Ej. 54"
                               required>

                        <small class="text-muted">
                            Indique la cantidad que contiene el producto.
                        </small>

                    </div>


                    <!-- UNIDAD DE MEDIDA -->

                    <div class="mb-3">

                        <label class="form-label">
                            Unidad de Medida
                        </label>

                        <select name="unidad_id"
                                class="form-control"
                                required>

                            <option value="">
                                Seleccione una unidad
                            </option>

                            <?php while ($u = $unidades->fetch_assoc()): ?>

                                <option value="<?= $u['id_unidad'] ?>">

                                    <?= htmlspecialchars($u['nombre_unidad']) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <!-- CATEGORÍA -->

                    <div class="mb-3">

                        <label class="form-label">
                            Categoría
                        </label>

                        <input type="text"
                               name="categoria_producto"
                               class="form-control"
                               placeholder="Ej. Polvos">

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="submit"
                            class="btn btn-success">

                        Guardar

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>