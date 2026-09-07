<?php

session_start();

$logged = $_SESSION['LOGGED'] ?? 0;

include("common/conexion.php");


/* =========================================================
   MENSAJES
========================================================= */

if (isset($_SESSION['mensaje'])) {

    $mensaje = $_SESSION['mensaje'];

    $tipo_mensaje =
        $_SESSION['tipo_mensaje'] ?? 'info';

    unset(
        $_SESSION['mensaje'],
        $_SESSION['tipo_mensaje']
    );
}


/* =========================================================
   FILTRO POR PRODUCTO
========================================================= */

$id_producto =
    isset($_GET['id_producto'])
        ? intval($_GET['id_producto'])
        : 0;


/* =========================================================
   CONDICIÓN DEL PRODUCTO
========================================================= */

$producto_sql = "";

if ($id_producto > 0) {

    $producto_sql =
        " AND l.producto_id = $id_producto ";

}


/* =========================================================
   LOTES ACTIVOS
=========================================================

   IMPORTANTE:

   Aquí agrupamos por producto.

   Si tienes:

   Ariel lote 1 = 2
   Ariel lote 2 = 2

   Registro mostrará:

   Ariel = 4

   Si utilizas 1:

   Ariel lote 1 = 1
   Ariel lote 2 = 2

   Registro mostrará:

   Ariel = 3

========================================================= */

$sql_activos = "

SELECT

    l.producto_id,

    p.nombre_comercial,

    pr.nombre AS nombre_proveedor,

    u.nombre_unidad,

    SUM(l.cantidad) AS cantidad,

    MIN(l.fecha_entrada) AS fecha_entrada,

    MIN(l.fecha_caducidad) AS fecha_caducidad,

    DATEDIFF(
        MIN(l.fecha_caducidad),
        CURDATE()
    ) AS dias_para_caducar

FROM lotes l

LEFT JOIN productos p
    ON l.producto_id = p.id_producto

LEFT JOIN proveedor pr
    ON l.proveedor_id = pr.id_proveedor

LEFT JOIN unidad_medida u
    ON p.unidad_id = u.id_unidad

WHERE
    l.estado = 1
    AND l.fecha_salida IS NULL
    AND l.cantidad > 0
    $producto_sql

GROUP BY
    l.producto_id,
    p.nombre_comercial,
    pr.nombre,
    u.nombre_unidad

ORDER BY
    MIN(l.fecha_caducidad) ASC

";


/* =========================================================
   EJECUTAR LOTES ACTIVOS
========================================================= */

$result =
    $conn->query($sql_activos);


/* =========================================================
   HISTORIAL DE SALIDAS
=========================================================

   La tabla salidas contiene:

   producto_id
   cantidad_usada
   fecha_salida

   Por eso aquí mostramos:

   cantidad_usada

   y NO lotes.cantidad.

========================================================= */

$sql_salidos = "

SELECT

    s.id_salida,

    s.producto_id,

    s.cantidad_usada,

    s.fecha_salida,

    p.nombre_comercial,

    u.nombre_unidad

FROM salidas s

LEFT JOIN productos p
    ON s.producto_id = p.id_producto

LEFT JOIN unidad_medida u
    ON p.unidad_id = u.id_unidad

WHERE 1 = 1

";


/* =========================================================
   FILTRO DEL HISTORIAL
========================================================= */

if ($id_producto > 0) {

    $sql_salidos .=
        " AND s.producto_id = $id_producto ";

}


$sql_salidos .= "

ORDER BY
    s.fecha_salida DESC,
    s.id_salida DESC

";


$result_salidos =
    $conn->query($sql_salidos);


/* =========================================================
   LOTES ACTIVOS
========================================================= */

$caducando = $conn->query("

    SELECT COUNT(*) AS total

    FROM lotes

    WHERE
        DATEDIFF(
            fecha_caducidad,
            CURDATE()
        ) BETWEEN 0 AND 7

        AND estado = 1

        AND fecha_salida IS NULL

        AND cantidad > 0

")->fetch_assoc()['total'];


/* =========================================================
   VENCIDOS
========================================================= */

$vencidos = $conn->query("

    SELECT COUNT(*) AS total

    FROM lotes

    WHERE
        fecha_caducidad < CURDATE()

        AND estado = 1

        AND fecha_salida IS NULL

        AND cantidad > 0

")->fetch_assoc()['total'];


/* =========================================================
   LOTES ACTIVOS
========================================================= */

$activos = $conn->query("

    SELECT COUNT(*) AS total

    FROM lotes

    WHERE
        estado = 1

        AND fecha_salida IS NULL

        AND cantidad > 0

")->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="es">

<head>

```
<meta charset="UTF-8">

<title>Registro</title>

<link
    rel="stylesheet"
    href="css/bootstrap.min.css"
>

<link
    rel="stylesheet"
    href="css/registro.css"
>
```

</head>

<body>

<?php include("navbar.php"); ?>

<div class="container py-4">

```
<!-- =====================================================
     MENSAJE
====================================================== -->

<?php if (isset($mensaje)): ?>

    <div
        class="alert alert-<?= htmlspecialchars($tipo_mensaje) ?> alert-dismissible fade show mb-4"
        role="alert"
    >

        <?= htmlspecialchars($mensaje) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

<?php endif; ?>



<!-- =====================================================
     TITULO
====================================================== -->

<div class="titulo-contenedor mb-4">

    <h2>
        Registro Completo de Lotes
    </h2>

</div>



<!-- =====================================================
     TARJETAS
====================================================== -->

<div class="row mb-4">


    <!-- LOTES ACTIVOS -->

    <div class="col-md-3">

        <div class="card text-white bg-success">

            <div class="card-body text-center">

                <h3>
                    <?= $activos ?>
                </h3>

                <p class="mb-0">
                    Lotes Activos
                </p>

            </div>

        </div>

    </div>



    <!-- CADUCANDO -->

    <div class="col-md-3">

        <div class="card text-white bg-warning">

            <div class="card-body text-center">

                <h3>
                    <?= $caducando ?>
                </h3>

                <p class="mb-0">
                    Caducan en 7 días
                </p>

            </div>

        </div>

    </div>



    <!-- VENCIDOS -->

    <div class="col-md-3">

        <div class="card text-white bg-danger">

            <div class="card-body text-center">

                <h3>
                    <?= $vencidos ?>
                </h3>

                <p class="mb-0">
                    Vencidos
                </p>

            </div>

        </div>

    </div>



    <!-- TOTAL PRODUCTOS -->

    <div class="col-md-3">

        <div class="card text-white bg-info">

            <div class="card-body text-center">

                <h3>

                    <?= $result
                        ? $result->num_rows
                        : 0
                    ?>

                </h3>

                <p class="mb-0">
                    Productos Activos
                </p>

            </div>

        </div>

    </div>


</div>



<!-- =====================================================
     TABLA DE INVENTARIO ACTUAL
====================================================== -->

<table
    class="table table-bordered table-striped align-middle mb-5"
>

    <thead class="table-dark">

        <tr>

            <th>
                Producto
            </th>

            <th>
                Proveedor
            </th>

            <th>
                Cantidad
            </th>

            <th>
                Entrada
            </th>

            <th>
                Caducidad
            </th>

            <th>
                Días Restantes
            </th>

            <th>
                Estado
            </th>

        </tr>

    </thead>


    <tbody>


    <?php if (
        $result &&
        $result->num_rows > 0
    ): ?>


        <?php while (
            $row =
            $result->fetch_assoc()
        ): ?>


            <?php

            $dias =
                intval(
                    $row['dias_para_caducar']
                );


            $clase_fila = '';


            if ($dias <= 0) {

                $clase_fila =
                    'table-danger';

            } elseif ($dias <= 7) {

                $clase_fila =
                    'table-warning';

            }

            ?>


            <tr
                class="<?= $clase_fila ?>"
            >


                <!-- PRODUCTO -->

                <td>

                    <?= htmlspecialchars(
                        $row['nombre_comercial'] ?? '-'
                    ) ?>

                </td>



                <!-- PROVEEDOR -->

                <td>

                    <?= htmlspecialchars(
                        $row['nombre_proveedor'] ?? '-'
                    ) ?>

                </td>



                <!-- CANTIDAD -->

                <td>

                    <span
                        class="badge bg-primary"
                    >

                        <?= htmlspecialchars(
                            $row['cantidad']
                        ) ?>

                    </span>

                </td>



                <!-- ENTRADA -->

                <td>

                    <?php if (
                        !empty(
                            $row['fecha_entrada']
                        )
                    ): ?>

                        <?= date(
                            'd/m/Y',
                            strtotime(
                                $row['fecha_entrada']
                            )
                        ) ?>

                    <?php else: ?>

                        -

                    <?php endif; ?>

                </td>



                <!-- CADUCIDAD -->

                <td>

                    <?php if (
                        !empty(
                            $row['fecha_caducidad']
                        )
                    ): ?>

                        <?= date(
                            'd/m/Y',
                            strtotime(
                                $row['fecha_caducidad']
                            )
                        ) ?>

                    <?php else: ?>

                        -

                    <?php endif; ?>

                </td>



                <!-- DIAS RESTANTES -->

                <td>

                    <span
                        class="badge
                        <?= $dias <= 0
                            ? 'bg-danger'
                            : (
                                $dias <= 7
                                    ? 'bg-warning'
                                    : 'bg-success'
                            )
                        ?>"
                    >

                        <?= $dias > 0
                            ? $dias . ' días'
                            : 'VENCIDO'
                        ?>

                    </span>

                </td>



                <!-- ESTADO -->

                <td>

                    <span
                        class="badge bg-success"
                    >

                        Activo

                    </span>

                </td>


            </tr>


        <?php endwhile; ?>


    <?php else: ?>


        <tr>

            <td
                colspan="7"
                class="text-center text-muted py-3"
            >

                No hay productos activos registrados.

            </td>

        </tr>


    <?php endif; ?>


    </tbody>

</table>



<!-- =====================================================
     HISTORIAL
====================================================== -->

<h4
    class="text-secondary mb-3"
>

    Historial de Productos Salidos del Inventario

</h4>



<table
    class="table table-bordered table-hover align-middle"
>


    <thead class="table-secondary">

        <tr>

            <th>
                Producto
            </th>

            <th>
                Cantidad
            </th>

            <th>
                Fecha Salida
            </th>

            <th>
                Estado
            </th>

        </tr>

    </thead>



    <tbody>


    <?php if (
        $result_salidos &&
        $result_salidos->num_rows > 0
    ): ?>


        <?php while (
            $row_s =
            $result_salidos->fetch_assoc()
        ): ?>


            <tr>


                <!-- PRODUCTO -->

                <td>

                    <?= htmlspecialchars(
                        $row_s['nombre_comercial'] ?? '-'
                    ) ?>

                </td>



                <!-- CANTIDAD UTILIZADA -->

                <td>

                    <span
                        class="badge bg-danger"
                    >

                        <?= htmlspecialchars(
                            $row_s['cantidad_usada']
                        ) ?>

                    </span>

                </td>



                <!-- FECHA SALIDA -->

                <td>

                    <?php if (
                        !empty(
                            $row_s['fecha_salida']
                        )
                    ): ?>

                        <?= date(
                            'd/m/Y',
                            strtotime(
                                $row_s['fecha_salida']
                            )
                        ) ?>

                    <?php else: ?>

                        -

                    <?php endif; ?>

                </td>



                <!-- ESTADO -->

                <td>

                    <span
                        class="badge bg-secondary"
                    >

                        Fuera de Stock

                    </span>

                </td>


            </tr>


        <?php endwhile; ?>


    <?php else: ?>


        <tr>

            <td
                colspan="4"
                class="text-center text-muted py-3"
            >

                No hay registros de salidas de productos.

            </td>

        </tr>


    <?php endif; ?>


    </tbody>


</table>

</div>

<script
    src="js/bootstrap.bundle.min.js"
></script>

</body>

</html>
