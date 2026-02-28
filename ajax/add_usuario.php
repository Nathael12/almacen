<?php
header("Content-Type: application/json");
include("../common/conexion.php");

$peticion = json_decode(file_get_contents('php://input', false));
$user = $peticion->usuario ?? '';
$pass = $peticion->password ?? '';
$pass = md5($pass);

$sql = "INSERT INTO usuarios(usuario, password) VALUES ('$user', '$pass');";
$result = $conn->query($sql);

if($result){
    echo 1;
}else{
    echo 0;
}