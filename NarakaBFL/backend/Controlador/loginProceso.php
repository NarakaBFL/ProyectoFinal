<?php
session_start();
require_once "conexion.php";

$email = $_POST["email"] ?? "";
$pass = $_POST["pass"] ?? "";

if (empty($email) || empty($pass)) {
    die("Error: Debe completar todos los campos.");
}

$sql = "SELECT * FROM usuario WHERE email = ?";
$stmt = $conexion->prepare($sql); 
$stmt->bind_param("s", $email);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($pass, $usuario["pass"])) {

        $_SESSION["id"] = $usuario["id"];
        $_SESSION["nombre"] = $usuario["nombre"];
        header("Location: ../Vista/perfil.php");
        exit();
    }else{
        echo "Contraseña incorrecta.";
    }
}else{
    echo "No se encontró un usuario con ese email.";
}
?>