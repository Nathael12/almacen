<div
    class="modal fade"
    id="modalUsarProducto"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                action="acciones/usar_producto.php"
                method="POST">


                <div class="modal-header">

                    <h5 class="modal-title">
                        Usar Producto
                    </h5>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">


                    <!-- PRODUCTO -->

                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            1. Seleccionar Producto
                        </label>


                        <select
                            name="id_producto"
                            id="usar_id_producto"
                            class="form-select"
                            onchange="alCambiarProducto(this.value)"
                            required>


                            <option value="">
                                Seleccione un producto...
                            </option>


                            <?php

                            $productos_modal =
                                $conn->query(
                                    "SELECT
                                        id_producto,
                                        nombre_comercial

                                    FROM productos

                                    WHERE estado = 1

                                    ORDER BY nombre_comercial ASC"
                                );


                            while (
                                $prod =
                                $productos_modal->fetch_assoc()
                            ):
                            ?>

                                <option
                                    value="<?= $prod['id_producto'] ?>">

                                    <?= htmlspecialchars(
                                        $prod['nombre_comercial']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>


                        </select>

                    </div>



                    <!-- LOTE -->

                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            2. Seleccionar Lote
                        </label>


                        <select
                            name="id_lote"
                            id="usar_id_lote"
                            class="form-select"
                            onchange="actualizarCantidadDisponible()"
                            required
                            disabled>


                            <option value="">
                                Seleccione primero un producto...
                            </option>


                        </select>


                        <small
                            class="text-muted"
                            id="info_disponibles">

                        </small>

                    </div>



                    <!-- CANTIDAD -->

                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            3. Cantidad a utilizar
                        </label>


                        <input
                            type="number"
                            id="usar_cantidad"
                            name="cantidad_usada"
                            class="form-control"
                            value="1"
                            min="1"
                            required
                            disabled>


                        <small
                            class="text-muted"
                            id="info_cantidad">

                            Seleccione un lote para ver
                            la cantidad disponible.

                        </small>

                    </div>



                    <!-- FECHA DE USO -->

                    <div class="mb-3">

                        <label
                            for="fecha_uso"
                            class="form-label fw-bold">

                            4. Fecha de uso

                        </label>


                        <input
                            type="date"
                            name="fecha_uso"
                            id="fecha_uso"
                            class="form-control"
                            value="<?= date('Y-m-d') ?>"
                            max="<?= date('Y-m-d') ?>"
                            required>


                        <small class="text-muted">

                            Selecciona la fecha en que realmente
                            se utilizó el producto.

                        </small>

                    </div>


                </div>


                <div class="modal-footer">


                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancelar

                    </button>


                    <button
                        type="submit"
                        class="btn btn-success"
                        id="btn_confirmar_salida"
                        disabled>

                        Confirmar Salida

                    </button>


                </div>


            </form>

        </div>

    </div>

</div>


<script>

let lotesDisponibles = [];


/*
|--------------------------------------------------------------------------
| CUANDO CAMBIA EL PRODUCTO
|--------------------------------------------------------------------------
*/

function alCambiarProducto(idProducto) {

    const selectLote =
        document.getElementById(
            'usar_id_lote'
        );


    const inputCantidad =
        document.getElementById(
            'usar_cantidad'
        );


    const infoDisponibles =
        document.getElementById(
            'info_disponibles'
        );


    const infoCantidad =
        document.getElementById(
            'info_cantidad'
        );


    const botonConfirmar =
        document.getElementById(
            'btn_confirmar_salida'
        );


    // Reiniciar

    selectLote.innerHTML =
        '<option value="">Cargando lotes...</option>';


    selectLote.disabled = true;


    inputCantidad.disabled = true;


    inputCantidad.value = 1;


    botonConfirmar.disabled = true;


    infoDisponibles.textContent = '';


    infoCantidad.textContent =
        'Seleccione un lote para ver la cantidad disponible.';


    if (!idProducto) {

        selectLote.innerHTML =
            '<option value="">Seleccione primero un producto...</option>';


        return;

    }


    fetch(
        'acciones/obtener_lotes.php?id_producto=' +
        encodeURIComponent(idProducto)
    )

    .then(response => {

        if (!response.ok) {

            throw new Error(
                'No se pudieron obtener los lotes.'
            );

        }


        return response.json();

    })


    .then(lotes => {


        lotesDisponibles = lotes;


        selectLote.innerHTML =
            '<option value="">Seleccione un lote...</option>';


        if (
            !Array.isArray(lotes) ||
            lotes.length === 0
        ) {


            selectLote.innerHTML =
                '<option value="">No hay lotes disponibles</option>';


            infoDisponibles.textContent =
                'Este producto no tiene unidades disponibles.';


            return;

        }


        lotes.forEach(lote => {


            const option =
                document.createElement('option');


            option.value =
                lote.id_lote;


            option.dataset.cantidad =
                lote.cantidad;


            option.textContent =
                'Lote #' +
                lote.id_lote +
                ' | Entrada: ' +
                lote.fecha_entrada_f +
                ' | Caduca: ' +
                lote.fecha_caducidad_f +
                ' | Disponibles: ' +
                lote.cantidad;


            selectLote.appendChild(
                option
            );


        });


        selectLote.disabled = false;


        infoDisponibles.textContent =
            lotes.length +
            ' lote(s) disponible(s).';


    })


    .catch(error => {


        console.error(error);


        selectLote.innerHTML =
            '<option value="">Error al cargar lotes</option>';


        infoDisponibles.textContent =
            'Ocurrió un error al obtener los lotes.';


    });

}



/*
|--------------------------------------------------------------------------
| CUANDO CAMBIA EL LOTE
|--------------------------------------------------------------------------
*/

function actualizarCantidadDisponible() {


    const selectLote =
        document.getElementById(
            'usar_id_lote'
        );


    const inputCantidad =
        document.getElementById(
            'usar_cantidad'
        );


    const infoCantidad =
        document.getElementById(
            'info_cantidad'
        );


    const botonConfirmar =
        document.getElementById(
            'btn_confirmar_salida'
        );


    const optionSeleccionada =
        selectLote.options[
            selectLote.selectedIndex
        ];


    if (
        !selectLote.value ||
        !optionSeleccionada
    ) {


        inputCantidad.disabled = true;


        botonConfirmar.disabled = true;


        infoCantidad.textContent =
            'Seleccione un lote para ver la cantidad disponible.';


        return;

    }


    const cantidadDisponible =
        parseInt(
            optionSeleccionada.dataset.cantidad
        );


    inputCantidad.disabled = false;


    inputCantidad.min = 1;


    inputCantidad.max =
        cantidadDisponible;


    inputCantidad.value = 1;


    botonConfirmar.disabled = false;


    infoCantidad.textContent =
        'Cantidad disponible en este lote: ' +
        cantidadDisponible;


}



/*
|--------------------------------------------------------------------------
| VALIDAR CANTIDAD MIENTRAS ESCRIBE
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {


        const inputCantidad =
            document.getElementById(
                'usar_cantidad'
            );


        inputCantidad.addEventListener(
            'input',
            function () {


                const max =
                    parseInt(
                        this.max
                    );


                let valor =
                    parseInt(
                        this.value
                    );


                if (
                    !isNaN(max) &&
                    valor > max
                ) {


                    this.value =
                        max;


                }


                if (
                    valor < 1 ||
                    isNaN(valor)
                ) {


                    this.value = 1;


                }


            }
        );


    }
);


/*
|--------------------------------------------------------------------------
| CARGAR PRODUCTO DESDE EL BOTÓN "USAR"
|--------------------------------------------------------------------------
*/

function cargarProducto(
    idProducto,
    nombreProducto
) {


    const selectProducto =
        document.getElementById(
            'usar_id_producto'
        );


    selectProducto.value =
        idProducto;


    alCambiarProducto(
        idProducto
    );


}

</script>