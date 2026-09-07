<?php

session_start();

if (!isset($_SESSION['usuario'])) {

    header("Location: login.php");
    exit();

}

include("common/conexion.php");


// MENSAJES

if (isset($_SESSION['mensaje'])) {

    $mensaje = $_SESSION['mensaje'];

    $tipo_mensaje =
        $_SESSION['tipo_mensaje'] ?? 'info';

    unset(
        $_SESSION['mensaje'],
        $_SESSION['tipo_mensaje']
    );

}


// BÚSQUEDA

$busqueda = "";


// CONSULTA DE PRODUCTOS

$sql = "
SELECT 
    p.id_producto,
    p.nombre_comercial,
    p.nombre_comun,
    p.presentacion,
    p.categoria_producto,
    p.estado,
    p.unidad_id,
    u.nombre_unidad,
    IFNULL(COUNT(l.id_lote), 0) AS total_lotes

FROM productos p

LEFT JOIN unidad_medida u
    ON p.unidad_id = u.id_unidad

LEFT JOIN lotes l
    ON p.id_producto = l.producto_id
    AND l.estado = 1
    AND l.cantidad > 0

WHERE p.estado = 1
";


if (!empty($_GET['q'])) {

    $busqueda = $_GET['q'];

    $sql .= "
        AND (
            p.nombre_comercial LIKE ?
            OR p.nombre_comun LIKE ?
            OR p.categoria_producto LIKE ?
        )
    ";

    $sql .= "
        GROUP BY
            p.id_producto,
            p.nombre_comercial,
            p.nombre_comun,
            p.presentacion,
            p.categoria_producto,
            p.estado,
            p.unidad_id,
            u.nombre_unidad
    ";

    $sql .= "
        ORDER BY p.nombre_comercial ASC
    ";


    $stmt = $conn->prepare($sql);

    $like = "%$busqueda%";

    $stmt->bind_param(
        "sss",
        $like,
        $like,
        $like
    );

    $stmt->execute();

    $result = $stmt->get_result();


} else {


    $sql .= "
        GROUP BY
            p.id_producto,
            p.nombre_comercial,
            p.nombre_comun,
            p.presentacion,
            p.categoria_producto,
            p.estado,
            p.unidad_id,
            u.nombre_unidad
    ";

    $sql .= "
        ORDER BY p.nombre_comercial ASC
    ";


    $result = $conn->query($sql);

}


// UNIDADES

$unidades =
    $conn->query("SELECT * FROM unidad_medida");

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Productos</title>

    <link rel="stylesheet"
          href="css/bootstrap.min.css">

    <link rel="stylesheet"
          href="css/registro.css">

</head>


<body>


<?php include("navbar.php"); ?>


<div class="container py-4">


    <!-- MENSAJE -->

    <?php if (isset($mensaje)): ?>

        <div class="alert alert-<?= htmlspecialchars($tipo_mensaje) ?>
                    alert-dismissible fade show mb-4"
             role="alert">

            <?= htmlspecialchars($mensaje) ?>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <!-- TÍTULO -->

    <div class="titulo-contenedor">

        <h2>
            Lista de Productos
        </h2>

        <br>

    </div>


    <!-- BUSCADOR -->

    <form method="get"
          class="d-flex mb-3">


        <input type="text"
               name="q"
               class="form-control me-2"
               placeholder="Buscar producto..."
               value="<?= htmlspecialchars($busqueda) ?>">


        <button class="btn btn-primary">

            Buscar

        </button>


        <button type="button"
                class="btn btn-success ms-2"
                data-bs-toggle="modal"
                data-bs-target="#modalAgregarProducto">

            Agregar

        </button>


    </form>


    <!-- TABLA -->

    <table class="table table-bordered table-striped">


        <thead class="table-dark">


            <tr>


                <th>
                    #
                </th>


                <th>
                    Nombre Comercial
                </th>


                <th>
                    Nombre Común
                </th>


                <th>
                    Presentación
                </th>


                <th>
                    Categoría
                </th>


                <th>
                    Total Lotes
                </th>


                <th>
                    Acciones
                </th>


            </tr>


        </thead>


        <tbody>


        <?php if ($result && $result->num_rows > 0): ?>


            <?php

            $num = 1;

            while ($row = $result->fetch_assoc()):

            ?>


            <tr>


                <!-- NÚMERO -->

                <td>

                    <?= $num++ ?>

                </td>


                <!-- NOMBRE COMERCIAL -->

                <td>

                    <?= htmlspecialchars(
                        $row['nombre_comercial']
                    ) ?>

                </td>


                <!-- NOMBRE COMÚN -->

                <td>

                    <?= htmlspecialchars(
                        $row['nombre_comun']
                    ) ?>

                </td>


                <!-- PRESENTACIÓN -->

                <td>

                    <?php if ($row['presentacion'] !== null): ?>


                        <?php

                        // Quitar ceros innecesarios
                        // 54.00 -> 54
                        // 1.50 -> 1.5

                        $presentacion =
                            rtrim(
                                rtrim(
                                    number_format(
                                        $row['presentacion'],
                                        2,
                                        '.',
                                        ''
                                    ),
                                    '0'
                                ),
                                '.'
                            );

                        ?>


                        <span class="badge bg-light text-dark border">

                            <?= htmlspecialchars($presentacion) ?>

                            <?= htmlspecialchars(
                                $row['nombre_unidad'] ?? ''
                            ) ?>

                        </span>


                    <?php else: ?>


                        <span class="text-muted">

                            -

                        </span>


                    <?php endif; ?>

                </td>


                <!-- CATEGORÍA -->

                <td>

                    <?= htmlspecialchars(
                        $row['categoria_producto']
                    ) ?>

                </td>


                <!-- TOTAL LOTES -->

                <td>

                    <span class="badge bg-light text-dark border">

                        <?= $row['total_lotes'] ?>

                    </span>

                </td>


                <!-- ACCIONES -->

                <td>


                    <div class="d-flex justify-content-center gap-2">


                        <!-- EDITAR -->

                        <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar<?= $row['id_producto'] ?>">

                            Editar

                        </button>


                        <!-- BORRAR -->

                        <button type="button"
                                class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalInactivarProducto"
                                data-id="<?= $row['id_producto'] ?>"
                                data-nombre="<?= htmlspecialchars(
                                    $row['nombre_comercial'],
                                    ENT_QUOTES
                                ) ?>">

                            Borrar

                        </button>


                        <!-- USAR -->

                        <button type="button"
                                class="btn btn-success btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalUsarProducto"
                                onclick="cargarProducto(
                                    <?= $row['id_producto'] ?>,
                                    '<?= htmlspecialchars(
                                        $row['nombre_comercial'],
                                        ENT_QUOTES
                                    ) ?>'
                                )">

                            Usar

                        </button>


                    </div>


                </td>


            </tr>


            <?php endwhile; ?>


        <?php else: ?>


            <tr>

                <td colspan="7"
                    class="text-center text-muted py-3">

                    No hay productos registrados activos.

                </td>

            </tr>


        <?php endif; ?>


        </tbody>


    </table>


</div>


<!-- MODALES -->

<?php include_once(
    'modals/modal_agregar_producto.php'
); ?>


<?php include_once(
    'modals/modal_editar_productos.php'
); ?>


<?php include_once(
    'modals/modal_usar_producto.php'
); ?>


<?php include_once(
    'modals/modal_borrar_producto.php'
); ?>


<!-- BOOTSTRAP -->

<script src="js/bootstrap.bundle.min.js"></script>


<script>


const modalInactivarProd =
    document.getElementById(
        'modalInactivarProducto'
    );


if (modalInactivarProd) {


    modalInactivarProd.addEventListener(
        'show.bs.modal',
        event => {


            const boton =
                event.relatedTarget;


            const idProducto =
                boton.getAttribute(
                    'data-id'
                );


            const nombreProducto =
                boton.getAttribute(
                    'data-nombre'
                );


            modalInactivarProd.querySelector(
                '#id_producto_modal'
            ).value = idProducto;


            modalInactivarProd.querySelector(
                '#nombre_producto_modal'
            ).textContent =
                nombreProducto;


        }
    );

}


</script>


</body>

</html>