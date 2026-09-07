<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include("common/conexion.php");


/* =========================================================
   TOTAL DE PRODUCTOS ACTIVOS
========================================================= */

$total_productos = 0;

$result_productos = $conn->query("

    SELECT COUNT(*) AS total

    FROM productos

    WHERE estado = 1

");

if ($result_productos) {

    $datos_productos =
        $result_productos->fetch_assoc();

    $total_productos =
        intval($datos_productos['total']);

}


/* =========================================================
   TOTAL DE LOTES DISPONIBLES
========================================================= */

$total_lotes = 0;

$result_lotes = $conn->query("

    SELECT COUNT(*) AS total

    FROM lotes

    WHERE estado = 1

    AND fecha_salida IS NULL

    AND cantidad > 0

");

if ($result_lotes) {

    $datos_lotes =
        $result_lotes->fetch_assoc();

    $total_lotes =
        intval($datos_lotes['total']);

}


/* =========================================================
   CANTIDAD TOTAL EN INVENTARIO
========================================================= */

$total_cantidad = 0;

$result_cantidad = $conn->query("

    SELECT IFNULL(
        SUM(cantidad),
        0
    ) AS total

    FROM lotes

    WHERE estado = 1

    AND fecha_salida IS NULL

    AND cantidad > 0

");

if ($result_cantidad) {

    $datos_cantidad =
        $result_cantidad->fetch_assoc();

    $total_cantidad =
        intval($datos_cantidad['total']);

}


/* =========================================================
   LOTES QUE CADUCAN EN LOS PRÓXIMOS 7 DÍAS
========================================================= */

$caducando = 0;

$result_caducando = $conn->query("

    SELECT COUNT(*) AS total

    FROM lotes

    WHERE estado = 1

    AND fecha_salida IS NULL

    AND cantidad > 0

    AND DATEDIFF(
        fecha_caducidad,
        CURDATE()
    ) BETWEEN 0 AND 7

");

if ($result_caducando) {

    $datos_caducando =
        $result_caducando->fetch_assoc();

    $caducando =
        intval($datos_caducando['total']);

}

$vencidos = 0;

$result_vencidos = $conn->query("

    SELECT COUNT(*) AS total

    FROM lotes

    WHERE estado = 1

    AND fecha_salida IS NULL

    AND cantidad > 0

    AND fecha_caducidad < CURDATE()

");

if ($result_vencidos) {

    $datos_vencidos =
        $result_vencidos->fetch_assoc();

    $vencidos =
        intval($datos_vencidos['total']);

}


/* =========================================================
   LOTES PRÓXIMOS A CADUCAR
========================================================= */

$proximos_caducar = $conn->query("

    SELECT

        l.id_lote,

        p.nombre_comercial,

        l.cantidad,

        l.fecha_caducidad,

        DATEDIFF(
            l.fecha_caducidad,
            CURDATE()
        ) AS dias_restantes

    FROM lotes l

    LEFT JOIN productos p

        ON l.producto_id =
        p.id_producto

    WHERE l.estado = 1

    AND l.fecha_salida IS NULL

    AND l.cantidad > 0

    AND l.fecha_caducidad IS NOT NULL

    AND l.fecha_caducidad >= CURDATE()

    ORDER BY
        l.fecha_caducidad ASC

    LIMIT 5

");


/* =========================================================
   ÚLTIMAS SALIDAS
========================================================= */

$ultimas_salidas = $conn->query("

    SELECT

        s.id_salida,

        p.nombre_comercial,

        s.cantidad_usada,

        s.fecha_salida,

        u.nombre_unidad

    FROM salidas s

    LEFT JOIN productos p

        ON s.producto_id =
        p.id_producto

    LEFT JOIN unidad_medida u

        ON p.unidad_id =
        u.id_unidad

    ORDER BY
        s.fecha_salida DESC,
        s.id_salida DESC

    LIMIT 5

");

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Inicio - Sistema de Almacén
    </title>


    <link
    rel="stylesheet"
    href="css/bootstrap.min.css"
    >

    <link
        rel="stylesheet"
        href="css/bootstrap-icons.css"
    >

    <link
        rel="stylesheet"
        href="css/index.css?v=2"
    >


    <!-- =====================================================
         ESTILOS ORIGINALES DEL INDEX
    ====================================================== -->

    <style>

        body {

            background:
                #f4f6f9;

        }


        /* ==========================================
           BIENVENIDA
        ========================================== */

        .dashboard-bienvenida {

            background:
                linear-gradient(
                    135deg,
                    #1a202c,
                    #2d3748
                );

            color:
                white;

            border-radius:
                16px;

            padding:
                30px;

            margin-bottom:
                25px;

            box-shadow:
                0 10px 25px
                rgba(0,0,0,0.12);

        }


        .dashboard-bienvenida h2 {

            font-weight:
                700;

        }


        .dashboard-bienvenida p {

            color:
                #cbd5e1;

            margin-bottom:
                0;

        }


        /* ==========================================
           TARJETAS ESTADÍSTICAS
        ========================================== */

        .stat-card {

            border:
                none;

            border-radius:
                14px;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

            height:
                100%;

            box-shadow:
                0 5px 15px
                rgba(0,0,0,0.06);

        }


        .stat-card:hover {

            transform:
                translateY(-5px);

            box-shadow:
                0 10px 25px
                rgba(0,0,0,0.12);

        }


        .stat-icon {

            width:
                55px;

            height:
                55px;

            border-radius:
                12px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                1.5rem;

        }


        .stat-number {

            font-size:
                1.8rem;

            font-weight:
                700;

        }


        .stat-title {

            color:
                #64748b;

            font-size:
                0.9rem;

            margin:
                0;

        }


        /* ==========================================
           CONTENEDORES
        ========================================== */

        .dashboard-card {

            background:
                white;

            border-radius:
                14px;

            padding:
                20px;

            box-shadow:
                0 5px 15px
                rgba(0,0,0,0.06);

            height:
                100%;

        }


        .dashboard-card-title {

            font-weight:
                700;

            margin-bottom:
                20px;

            color:
                #1e293b;

        }


        /* ==========================================
           ACCESOS RÁPIDOS
        ========================================== */

        .acceso-rapido {

            text-decoration:
                none;

            color:
                inherit;

        }


        .acceso-item {

            padding:
                15px;

            border:
                1px solid #e2e8f0;

            border-radius:
                12px;

            text-align:
                center;

            transition:
                all 0.2s ease;

            height:
                100%;

        }


        .acceso-item:hover {

            background:
                #f8fafc;

            transform:
                translateY(-3px);

            box-shadow:
                0 8px 18px
                rgba(0,0,0,0.08);

        }


        .acceso-icon {

            font-size:
                1.8rem;

            margin-bottom:
                8px;

        }

    </style>


    <!-- =====================================================
         ESTILO NUEVO DEL DASHBOARD
    ====================================================== -->

    <link
        rel="stylesheet"
        href="css/index.css"
    >

</head>


<body>


<?php include("navbar.php"); ?>


<div class="container py-4">


    <!-- ==========================================
         BIENVENIDA
    =========================================== -->

    <div class="dashboard-bienvenida">

        <h2>

            Bienvenido,
            <?= htmlspecialchars(
                $_SESSION['nombre']
                ?? $_SESSION['usuario']
            ) ?>

            👋

        </h2>


        <p>

            Aquí tienes un resumen general
            del estado actual del almacén.

        </p>

    </div>



    <!-- ==========================================
         ESTADÍSTICAS
    =========================================== -->

    <div class="row g-4 mb-4">


        <!-- PRODUCTOS -->

        <div class="col-md-6 col-lg-3">

            <div class="card stat-card">

                <div class="card-body d-flex align-items-center">

                    <div
                        class="stat-icon bg-primary bg-opacity-10 text-primary me-3"
                    >

                        <i class="bi bi-box-seam"></i>

                    </div>


                    <div>

                        <div
                            class="stat-number"
                        >

                            <?= $total_productos ?>

                        </div>


                        <p
                            class="stat-title"
                        >

                            Productos Activos

                        </p>

                    </div>

                </div>

            </div>

        </div>



        <!-- LOTES -->

        <div class="col-md-6 col-lg-3">

            <div class="card stat-card">

                <div class="card-body d-flex align-items-center">

                    <div
                        class="stat-icon bg-success bg-opacity-10 text-success me-3"
                    >

                        <i class="bi bi-layers"></i>

                    </div>


                    <div>

                        <div
                            class="stat-number"
                        >

                            <?= $total_lotes ?>

                        </div>


                        <p
                            class="stat-title"
                        >

                            Lotes Disponibles

                        </p>

                    </div>

                </div>

            </div>

        </div>



        <!-- CANTIDAD -->

        <div class="col-md-6 col-lg-3">

            <div class="card stat-card">

                <div class="card-body d-flex align-items-center">

                    <div
                        class="stat-icon bg-info bg-opacity-10 text-info me-3"
                    >

                        <i class="bi bi-boxes"></i>

                    </div>


                    <div>

                        <div
                            class="stat-number"
                        >

                            <?= number_format(
                                $total_cantidad
                            ) ?>

                        </div>


                        <p
                            class="stat-title"
                        >

                            Unidades en Inventario

                        </p>

                    </div>

                </div>

            </div>

        </div>



        <!-- CADUCANDO -->

        <div class="col-md-6 col-lg-3">

            <div class="card stat-card">

                <div class="card-body d-flex align-items-center">

                    <div
                        class="stat-icon bg-warning bg-opacity-10 text-warning me-3"
                    >

                        <i class="bi bi-exclamation-triangle"></i>

                    </div>


                    <div>

                        <div
                            class="stat-number"
                        >

                            <?= $caducando ?>

                        </div>


                        <p
                            class="stat-title"
                        >

                            Caducan en 7 días

                        </p>

                    </div>

                </div>

            </div>

        </div>


    </div>



    <!-- ==========================================
         ALERTA VENCIDOS
    =========================================== -->

    <?php if ($vencidos > 0): ?>

        <div
            class="alert alert-danger d-flex align-items-center"
        >

            <i
                class="bi bi-exclamation-octagon-fill fs-4 me-3"
            ></i>


            <div>

                <strong>

                    Atención:

                </strong>

                Existen

                <?= $vencidos ?>

                lote(s) con productos vencidos
                que todavía aparecen activos.

            </div>

        </div>

    <?php endif; ?>



    <!-- ==========================================
         CONTENIDO PRINCIPAL
    =========================================== -->

    <div class="row g-4 mb-4">


        <!-- LOTES PRÓXIMOS A CADUCAR -->

        <div class="col-lg-7">

            <div class="dashboard-card">

                <h5
                    class="dashboard-card-title"
                >

                    <i
                        class="bi bi-clock-history text-warning me-2"
                    ></i>

                    Próximos a Caducar

                </h5>


                <div
                    class="table-responsive"
                >

                    <table
                        class="table table-hover align-middle mb-0"
                    >

                        <thead>

                            <tr>

                                <th>
                                    Producto
                                </th>

                                <th>
                                    Cantidad
                                </th>

                                <th>
                                    Caducidad
                                </th>

                                <th>
                                    Estado
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php if (
                            $proximos_caducar
                            &&
                            $proximos_caducar->num_rows > 0
                        ): ?>


                            <?php while (
                                $lote =
                                $proximos_caducar->fetch_assoc()
                            ): ?>


                                <?php

                                $dias =
                                    intval(
                                        $lote[
                                            'dias_restantes'
                                        ]
                                    );

                                ?>


                                <tr>


                                    <td>

                                        <?= htmlspecialchars(
                                            $lote[
                                                'nombre_comercial'
                                            ]
                                            ?? '-'
                                        ) ?>

                                    </td>


                                    <td>

                                        <span
                                            class="badge bg-primary"
                                        >

                                            <?= htmlspecialchars(
                                                $lote[
                                                    'cantidad'
                                                ]
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <?= date(
                                            'd/m/Y',
                                            strtotime(
                                                $lote[
                                                    'fecha_caducidad'
                                                ]
                                            )
                                        ) ?>

                                    </td>


                                    <td>

                                        <span
                                            class="badge
                                            <?= $dias <= 7
                                                ? 'bg-warning text-dark'
                                                : 'bg-success'
                                            ?>"
                                        >

                                            <?= $dias ?>

                                            días

                                        </span>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="4"
                                    class="text-center text-muted py-4"
                                >

                                    <i
                                        class="bi bi-check-circle fs-4 d-block mb-2 text-success"
                                    ></i>

                                    No hay lotes próximos
                                    a caducar.

                                </td>

                            </tr>


                        <?php endif; ?>


                        </tbody>

                    </table>

                </div>


                <div
                    class="text-end mt-3"
                >

                    <a
                        href="registro.php"
                        class="btn btn-outline-primary btn-sm"
                    >

                        Ver registro completo

                    </a>

                </div>

            </div>

        </div>



        <!-- ÚLTIMAS SALIDAS -->

        <div class="col-lg-5">

            <div class="dashboard-card">

                <h5
                    class="dashboard-card-title"
                >

                    <i
                        class="bi bi-box-arrow-up-right text-danger me-2"
                    ></i>

                    Últimas Salidas

                </h5>


                <?php if (
                    $ultimas_salidas
                    &&
                    $ultimas_salidas->num_rows > 0
                ): ?>


                    <div
                        class="list-group list-group-flush"
                    >


                    <?php while (
                        $salida =
                        $ultimas_salidas->fetch_assoc()
                    ): ?>


                        <div
                            class="list-group-item px-0"
                        >

                            <div
                                class="d-flex justify-content-between align-items-center"
                            >

                                <div>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $salida[
                                                'nombre_comercial'
                                            ]
                                            ?? '-'
                                        ) ?>

                                    </strong>


                                    <div
                                        class="small text-muted"
                                    >

                                        <?= !empty(
                                            $salida[
                                                'fecha_salida'
                                            ]
                                        )
                                            ? date(
                                                'd/m/Y',
                                                strtotime(
                                                    $salida[
                                                        'fecha_salida'
                                                    ]
                                                )
                                            )
                                            : '-'
                                        ?>

                                    </div>

                                </div>


                                <span
                                    class="badge bg-danger"
                                >

                                    -

                                    <?= htmlspecialchars(
                                        $salida[
                                            'cantidad_usada'
                                        ]
                                    ) ?>

                                    <?= htmlspecialchars(
                                        $salida[
                                            'nombre_unidad'
                                        ]
                                        ?? ''
                                    ) ?>

                                </span>

                            </div>

                        </div>


                    <?php endwhile; ?>


                    </div>


                <?php else: ?>


                    <div
                        class="text-center text-muted py-4"
                    >

                        <i
                            class="bi bi-inbox fs-2 d-block mb-2"
                        ></i>

                        No hay salidas registradas.

                    </div>


                <?php endif; ?>


                <div
                    class="text-end mt-3"
                >

                    <a
                        href="registro.php"
                        class="btn btn-outline-danger btn-sm"
                    >

                        Ver historial completo

                    </a>

                </div>

            </div>

        </div>


    </div>



    <!-- ==========================================
         ACCESOS RÁPIDOS
    =========================================== -->

    <div
        class="dashboard-card"
    >


        <h5
            class="dashboard-card-title"
        >

            <i
                class="bi bi-lightning-charge text-primary me-2"
            ></i>

            Accesos Rápidos

        </h5>


        <div
            class="row g-3"
        >


            <!-- PRODUCTOS -->

            <div
                class="col-md-3"
            >

                <a
                    href="productos.php"
                    class="acceso-rapido"
                >

                    <div
                        class="acceso-item"
                    >

                        <div
                            class="acceso-icon text-primary"
                        >

                            <i
                                class="bi bi-box"
                            ></i>

                        </div>


                        <strong>

                            Productos

                        </strong>


                        <div
                            class="small text-muted mt-1"
                        >

                            Administrar productos

                        </div>

                    </div>

                </a>

            </div>



            <!-- LOTES -->

            <div
                class="col-md-3"
            >

                <a
                    href="lotes.php"
                    class="acceso-rapido"
                >

                    <div
                        class="acceso-item"
                    >

                        <div
                            class="acceso-icon text-success"
                        >

                            <i
                                class="bi bi-layers"
                            ></i>

                        </div>


                        <strong>

                            Lotes

                        </strong>


                        <div
                            class="small text-muted mt-1"
                        >

                            Controlar lotes

                        </div>

                    </div>

                </a>

            </div>



            <!-- REGISTRO -->

            <div
                class="col-md-3"
            >

                <a
                    href="registro.php"
                    class="acceso-rapido"
                >

                    <div
                        class="acceso-item"
                    >

                        <div
                            class="acceso-icon text-warning"
                        >

                            <i
                                class="bi bi-clipboard-data"
                            ></i>

                        </div>


                        <strong>

                            Registro

                        </strong>


                        <div
                            class="small text-muted mt-1"
                        >

                            Inventario e historial

                        </div>

                    </div>

                </a>

            </div>



            <!-- REPORTES -->

            <div
                class="col-md-3"
            >

                <a
                    href="reportes.php"
                    class="acceso-rapido"
                >

                    <div
                        class="acceso-item"
                    >

                        <div
                            class="acceso-icon text-danger"
                        >

                            <i
                                class="bi bi-file-earmark-bar-graph"
                            ></i>

                        </div>


                        <strong>

                            Reportes

                        </strong>


                        <div
                            class="small text-muted mt-1"
                        >

                            Generar reportes

                        </div>

                    </div>

                </a>

            </div>


        </div>


    </div>


</div>


<script
    src="js/bootstrap.bundle.min.js"
></script>


</body>

</html>