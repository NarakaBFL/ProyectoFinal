<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inicio de sesión</title>
    <script src="../js/validaciones.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
<h1>Inicio de sesión</h1>

<form action="../Controlador/loginProceso.php" method="POST" onsubmit="return validarLogin();">

    <label>Email:</label>
    <input type="email" name="email" id="email"><br><br>

    <label>Contraseña:</label>
    <input type="password" name="pass" id="pass"><br><br>

    <button type="submit">Ingresar</button>
</form>

<p>¿No tenés una cuenta?<a href="registro.php">Registrate aquí</a></p>
        </div>
</body>
</html>