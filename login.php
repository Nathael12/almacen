<?php
session_start();
include("common/conexion.php");
$error = "";
if (isset($_POST['login'])) {
    $usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM usuarios WHERE usuario='$usuario'");
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['nombre'] = $user['nombre'];

            header("Location: index.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "El usuario no existe.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema Almacén - Iniciar Sesión</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="login-container">
    <div class="login-card">

        <div class="user-icon">
            <i class="fa-solid fa-user"></i>
        </div>

        <h2 class="title">Sistema Almacén</h2>
        <p class="subtitle">Inicio de sesión</p>

        <?php if($error): ?>
            <div class="alert-custom">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group-custom">
                <div class="input-icon">
                    <i class="fa-solid fa-user"></i>
                </div>
                <input 
                    type="text"
                    name="usuario"
                    class="form-control-custom"
                    placeholder="Ingresa tu usuario"
                    required
                >
            </div>

            <div class="input-group-custom">
                <div class="input-icon">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <input 
                    type="password"
                    name="password"
                    class="form-control-custom"
                    placeholder="Ingresa tu contraseña"
                    required
                >
            </div>

            <button type="submit" name="login" class="btn-login">
                Iniciar Sesión
            </button>
        </form>

    </div>
</div>

</body>
</html>