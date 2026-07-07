<?php
session_start();
require_once "../Controlador/conexion.php";

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"] ?? 0;

$sql = "SELECT * FROM usuario WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$usuario = $stmt->get_result()->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de usuario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">

<h1>Perfil de <?php echo $usuario["nombre"]; ?></h1>

<p>Email: <?php echo $usuario["email"]; ?></p>

<?php if ($usuario["img"] != null): ?>
    <img src="../uploads/<?php echo $usuario["img"]; ?>" width="150">
<?php else: ?>
    <p>Sin imagen de perfil.</p>
<?php endif; ?>

<br><br>

<a href="perfil.php">Volver a mi perfil</a>

    </div>
</body>

</html>