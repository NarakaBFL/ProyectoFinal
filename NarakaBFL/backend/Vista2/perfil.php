<?php
session_start();
require_once "../Controlador/conexion.php";

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION["id"];

$sql = "SELECT * FROM usuario WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

$usuarios = $conexion->query("SELECT id, nombre, email, img FROM usuario");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Perfil de usuario</h1>

<h2>Bienvenido, <?php echo $usuario["nombre"]; ?></h2>

<p>Email: <?php echo $usuario["email"]; ?></p>

<?php if ($usuario["img"] != null): ?>
    <img src="../uploads/<?php echo $usuario["img"]; ?>" width="150">
<?php else: ?>
    <p>Sin imagen de perfil.</p>
<?php endif; ?>

<br><br>

<a href="../Controlador/logout.php">
    <button>Cerrar sesión</button>
</a>

<hr>

<h2>Usuarios registrados</h2>

<ul>
<?php while ($fila = $usuarios->fetch_assoc()): ?>
    <li>
        <a href="perfil_usuario.php?id=<?php echo $fila["id"]; ?>">
            <?php echo $fila["nombre"]; ?>
        </a>
    </li>
<?php endwhile; ?>
</ul>

    </div>
</body>

</html>