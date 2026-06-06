<div class="modal fade" id="modalInactivarProducto" tabindex="-1" aria-labelledby="modalInactivarProductoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalInactivarProductoLabel">
                    <i class="bi bi-exclamation-triangle-fill"></i> ¿Dar de baja este producto?
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="acciones/borrar_producto.php" method="POST">
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas marcar como **inactivo** el producto: <strong id="nombre_producto_modal"></strong>?</p>
                    <p class="text-muted small">
                        Al hacerlo, el artículo ya no estará disponible para crear nuevos lotes ni movimientos, pero se guardará en la base de datos para no alterar las salidas anteriores.
                    </p>
                    
                    <input type="hidden" name="id_producto" id="id_producto_modal">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Baja</button>
                </div>
            </form>
        </div>
    </div>
</div>