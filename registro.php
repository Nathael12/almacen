<?php
session_start();
$logged = $_SESSION['LOGGED'] ?? 0;

if ($logged == 0) {
    //header('Location: index.php');
}

include("common/conexion.php");

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'info';
    unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
}

$id_producto= $_GET["id_producto"] ?? '';
$producto_sql= $id_producto === '' ? "": " AND l.producto_id = '$id_producto'";

$sql = "
SELECT l.id_lote, l.fecha_entrada, l.fecha_caducidad, l.fecha_salida, l.estado,
       p.nombre_comercial, pr.nombre AS nombre_proveedor, u.nombre_unidad,
       DATEDIFF(l.fecha_caducidad, CURDATE()) as dias_para_caducar
FROM lotes l
LEFT JOIN productos p ON l.producto_id = p.id_producto
LEFT JOIN proveedor pr ON l.proveedor_id = pr.id_proveedor
LEFT JOIN unidad_medida u ON p.unidad_id = u.id_unidad
WHERE l.estado = 1 $producto_sql
ORDER BY l.fecha_caducidad ASC
";

$result = $conn->query($sql);

$caducando = $conn->query("SELECT COUNT(*) as total FROM lotes WHERE DATEDIFF(fecha_caducidad, CURDATE()) BETWEEN 0 AND 7 AND estado = 1")->fetch_assoc()['total'];
$vencidos = $conn->query("SELECT COUNT(*) as total FROM lotes WHERE fecha_caducidad < CURDATE() AND estado = 1")->fetch_assoc()['total'];
$activos = $conn->query("SELECT COUNT(*) as total FROM lotes WHERE estado = 1")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/registro.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <div class="container py-4">

        <?php if (isset($mensaje)): ?>
        <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show mb-4" role="alert">
            <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="titulo-contenedor">
            <h2>Registro Completo</h2>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h3><?= $activos ?></h3>
                        <p>Lotes Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h3><?= $caducando ?></h3>
                        <p>Caducan en 7 días</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body text-center">
                        <h3><?= $vencidos ?></h3>
                        <p>Vencidos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h3><?= $result->num_rows ?></h3>
                        <p>Total Lotes</p>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Proveedor</th>
                    <th>Entrada</th>
                    <th>Caducidad</th>
                    <th>Salida</th>
                    <th>Días Restantes</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                    $dias = $row['dias_para_caducar'];
                    $clase_fila = '';
                    if ($dias <= 0) $clase_fila = 'table-danger';
                    elseif ($dias <= 7) $clase_fila = 'table-warning';
                    ?>
                    <tr class="<?= $clase_fila ?>">
                        <td><strong>#<?= $row['id_lote'] ?></strong></td>
                        <td><?= htmlspecialchars($row['nombre_comercial']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_proveedor']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['fecha_entrada'])) ?></td>
                        <td>
                            <?= date('d/m/Y', strtotime($row['fecha_caducidad'])) ?>
                            <?php if ($dias <= 7): ?>
                                <span class="badge bg-danger ms-1"><?= $dias ?> días</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['fecha_salida'] ? date('d/m/Y', strtotime($row['fecha_salida'])) : '-' ?></td>
                        <td>
                            <span class="badge <?= $dias <= 0 ? 'bg-danger' : ($dias <= 7 ? 'bg-warning' : 'bg-success') ?>">
                                <?= $dias > 0 ? $dias . ' días' : 'VENCIDO' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['estado'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-white">No hay registros</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>