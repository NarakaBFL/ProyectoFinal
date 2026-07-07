<?php
require_once "conexion.php";

$nombre = $_POST["nombre"] ?? "";
$email = $_POST["email"] ?? "";
$pass = $_POST["pass"] ?? "";
$passOK = $_POST["passOK"] ?? "";

if (empty($nombre) || empty($email) || empty($pass) || empty($passOK)) {
    die("Error: Completa todos los campos.");
}

if (strlen($pass) < 8) {
    die("Error: La contraseña debe tener mínimo 8 caracteres.");
}

if ($pass !== $passOK) {
    die ("Error: Las contraseñas no coinciden.");
}

$passHash = password_hash($pass, PASSWORD_DEFAULT);

$nombreImagen = null; 

if (isset($_FILES["img"]) && $_FILES["img"]["error"] == 0) {
    $nombreImagen = uniqid() . "_" . $_FILES["img"]["name"];
    $rutaTemporal = $_FILES["img"]["tmp_name"];
    $rutaDestino = "../uploads/" . $nombreImagen;

    move_uploaded_file($rutaTemporal, $rutaDestino);
}

$sql = "INSERT INTO usuario (nombre, email, pass, img) VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssss", $nombre, $email, $passHash, $nombreImagen);

if ($stmt->execute()) {
    header("Location: ../Vista/login.php");
    exit();
}else{
    die("Error, es posible que el email ya exista.");
}
?>
