<div class="modal fade" id="modalInactivarLote" tabindex="-1" aria-labelledby="modalInactivarLoteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalInactivarLoteLabel"><i class="bi bi-exclamation-triangle-fill"></i> ¿Marcar lote como terminado?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           <form action="acciones/borrar_lote.php" method="POST">
                <div class="modal-body">
                    <p>Estás a punto de marcar como inactivo el lote del producto: <strong id="nombre_producto_modal"></strong>.</p>
                    <p class="text-muted small">El lote dejará de estar disponible para consumos, pero se conservará en el sistema para no alterar los reportes de salidas y requerimientos diarios anteriores.</p>
                    
                    <input type="hidden" name="id_lote" id="id_lote_modal">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Sí, Inactivar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const modalInactivar = document.getElementById('modalInactivarLote');
if (modalInactivar) {
    modalInactivar.addEventListener('show.bs.modal', event => {
        const boton = event.relatedTarget;
        
        // Extraer la información de los atributos data-*
        const idLote = boton.getAttribute('data-id');
        const nombreProducto = boton.getAttribute('data-nombre');
        
        // Rellenar los campos del modal
        modalInactivar.querySelector('#id_lote_modal').value = idLote;
        modalInactivar.querySelector('#nombre_producto_modal').textContent = nombreProducto;
    });
}
</script>