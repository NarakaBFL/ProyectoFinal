<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <script src="../js/validaciones.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
    
<h1>Registro de usuario</h1>

<form action="../Controlador/registroProceso.php" method="POST" enctype="multipart/form-data" onsubmit="return validarRegistro();">

    <label>Nombre:</label>
    <input type="text" name="nombre" id="nombre"><br><br>

    <label>Email:</label>
    <input type="email" name="email" id="email"><br><br>

    <label>Contraseña:</label>
    <input type="password" name="pass" id="pass"><br><br>

    <label>Confirmar Contraseña:</label>
    <input type="password" name="passOK" id="passOK"><br><br>

    <label>Foto de Perfil:</label>
    <input type="file" name="img" id="img"><br><br>

    <button type="submit">Registrarse</button>
</form>

<p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
    </div>

</body>
</html>