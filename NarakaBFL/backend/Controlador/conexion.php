<?php
$conexion = new mysqli("localhost", "root", "", "base_usuarios");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>