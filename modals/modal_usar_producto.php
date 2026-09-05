<div class="modal fade" id="modalUsarProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="acciones/usar_producto.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Salida de Lotes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- 1. Producto -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Seleccionar Producto</label>
                        <select name="id_producto" id="usar_id_producto" class="form-select" onchange="alCambiarProducto(this.value)" required>
                            <option value="">Seleccione un producto...</option>
                            <?php 
                            $productos_modal = $conn->query("SELECT id_producto, nombre_comercial FROM productos WHERE estado = 1 ORDER BY nombre_comercial ASC");
                            while ($prod = $productos_modal->fetch_assoc()): 
                            ?>
                                <option value="<?= $prod['id_producto'] ?>">
                                    <?= htmlspecialchars($prod['nombre_comercial']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- 2. Cantidad de lotes a usar -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">2. Cantidad de Lotes / Unidades a Consumir</label>
                        <input type="number" id="usar_cantidad" name="cantidad_usada" class="form-control" value="1" min="1" onchange="generarSelectsLotes()" onkeyup="generarSelectsLotes()" required disabled>
                        <small class="text-muted" id="info_disponibles"></small>
                    </div>

                    <!-- 3. Contenedor dinámico de Lotes -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">3. Seleccionar Lote(s) a Consumir</label>
                        <div id="contenedor_lotes">
                            <p class="text-muted small mb-0">Seleccione primero un producto.</p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btn_confirmar_salida" disabled>Confirmar Salida</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let lotesDisponibles = [];

function cargarProducto(id, nombre) {
    const selectProducto = document.getElementById("usar_id_producto");
    if (selectProducto) {
        selectProducto.value = id;
        alCambiarProducto(id);
    }
}

function alCambiarProducto(idProducto) {
    const inputCantidad = document.getElementById("usar_cantidad");
    const contenedor = document.getElementById("contenedor_lotes");
    const infoDisp = document.getElementById("info_disponibles");
    const btnConfirmar = document.getElementById("btn_confirmar_salida");

    if (!idProducto) {
        inputCantidad.disabled = true;
        btnConfirmar.disabled = true;
        contenedor.innerHTML = '<p class="text-muted small mb-0">Seleccione primero un producto.</p>';
        infoDisp.textContent = "";
        return;
    }

    contenedor.innerHTML = '<p class="text-muted small mb-0">Cargando lotes disponibles...</p>';

    fetch(`acciones/obtener_lotes.php?id_producto=${idProducto}`)
        .then(response => response.json())
        .then(lotes => {
            lotesDisponibles = lotes;
            infoDisp.textContent = `Lotes disponibles: ${lotes.length}`;

            if (lotes.length === 0) {
                inputCantidad.disabled = true;
                btnConfirmar.disabled = true;
                contenedor.innerHTML = '<p class="text-danger small mb-0">No hay lotes disponibles para este producto.</p>';
            } else {
                inputCantidad.disabled = false;
                inputCantidad.max = lotes.length;
                inputCantidad.value = 1;
                btnConfirmar.disabled = false;
                generarSelectsLotes();
            }
        })
        .catch(error => {
            console.error("Error al cargar lotes:", error);
            contenedor.innerHTML = '<p class="text-danger small mb-0">Error al cargar lotes.</p>';
        });
}

function generarSelectsLotes() {
    const cantidad = parseInt(document.getElementById("usar_cantidad").value) || 0;
    const contenedor = document.getElementById("contenedor_lotes");
    contenedor.innerHTML = "";

    if (cantidad <= 0 || lotesDisponibles.length === 0) {
        return;
    }

    const cantidadAjustada = Math.min(cantidad, lotesDisponibles.length);

    for (let i = 0; i < cantidadAjustada; i++) {
        const divGroup = document.createElement("div");
        divGroup.className = "mb-2";

        const label = document.createElement("label");
        label.className = "form-label text-muted small mb-1";
        label.textContent = `Lote #${i + 1}:`;

        const select = document.createElement("select");
        select.name = "id_lotes[]"; // Array de IDs de lotes
        select.className = "form-select form-select-sm";
        select.required = true;

        const optDefault = document.createElement("option");
        optDefault.value = "";
        optDefault.textContent = "-- Seleccione lote --";
        select.appendChild(optDefault);

        lotesDisponibles.forEach((lote, index) => {
            const option = document.createElement("option");
            option.value = lote.id_lote;
            option.textContent = `Lote ID: ${lote.id_lote} (Entrada: ${lote.fecha_entrada_f}) - Vence: ${lote.fecha_caducidad_f}`;
            
            // Auto-selecciona opciones consecutivas por defecto si hay bastantes
            if (index === i) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        divGroup.appendChild(label);
        divGroup.appendChild(select);
        contenedor.appendChild(divGroup);
    }
}
</script>