<div class="modal fade" id="modalUsarProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="acciones/usar_producto.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Usar Producto desde Lote Específico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id_producto" id="usar_id_producto">

                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <input type="text" id="usar_nombre_producto" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Seleccionar Lote Disponible</label>
                        <select name="id_lote" id="usar_lote_select" class="form-control" onchange="actualizarMaximo()" required>
                            <option value="">Cargando lotes...</option>
                        </select>
                        <div id="lote_ayuda" class="form-text text-primary"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cantidad a utilizar</label>
                        <input type="number" name="cantidad" id="usar_cantidad" class="form-control" min="1" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Salida</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cargarProducto(id, nombre) {
    document.getElementById("usar_id_producto").value = id;
    document.getElementById("usar_nombre_producto").value = nombre;
    
    const selectLote = document.getElementById("usar_lote_select");
    const ayuda = document.getElementById("lote_ayuda");
    const inputCantidad = document.getElementById("usar_cantidad");
    
    selectLote.innerHTML = '<option value="">Cargando lotes...</option>';
    ayuda.textContent = "";
    inputCantidad.value = "";

    // Petición asíncrona para traer los lotes de este producto
    fetch(`acciones/obtener_lotes.php?id_producto=${id}`)
        .then(response => response.json())
        .then(lotes => {
            selectLote.innerHTML = '<option value="">Seleccione un lote...</option>';
            
            if (lotes.length === 0) {
                selectLote.innerHTML = '<option value="">No hay lotes con existencias</option>';
                return;
            }

            lotes.forEach(lote => {
                const option = document.createElement("option");
                option.value = lote.id_lote;
                option.dataset.max = lote.cantidad; // Guardamos el stock en el atributo data
                option.textContent = `Lote ID: ${lote.id_lote} (Disp: ${lote.cantidad}) - Vence: ${lote.fecha_f}`;
                selectLote.appendChild(option);
            });
        })
        .catch(error => {
            console.error("Error al cargar lotes:", error);
            selectLote.innerHTML = '<option value="">Error al cargar los lotes</option>';
        });
}

// Cambia dinámicamente el límite permitido en el input de cantidad según el lote elegido
function actualizarMaximo() {
    const select = document.getElementById("usar_lote_select");
    const selectedOption = select.options[select.selectedIndex];
    const inputCantidad = document.getElementById("usar_cantidad");
    const ayuda = document.getElementById("lote_ayuda");

    if (selectedOption && selectedOption.value !== "") {
        const maximo = selectedOption.dataset.max;
        inputCantidad.max = maximo; // Evita que escriban más de lo que hay
        ayuda.textContent = `Cantidad máxima permitida en este lote: ${maximo}`;
    } else {
        inputCantidad.removeAttribute("max");
        ayuda.textContent = "";
    }
}
</script>