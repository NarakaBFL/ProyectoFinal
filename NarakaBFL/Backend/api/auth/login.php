<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();

$email = strtolower(trim($body['email'] ?? ''));
$password = $body['password'] ?? '';

if ($email === '' || $password === '') {
    responder(400, null, 'Email y contraseña son obligatorios');
}

$stmt = $pdo->prepare("
SELECT u.id_usuario, u.nombre, u.apellido, u.email,
u.contrasena, u.id_rol, u.activo, r.nombre_rol
FROM usuario u
INNER JOIN rol r ON u.id_rol = r.id_rol
WHERE u.email = ?
LIMIT 1
");

$stmt->execute([$email]);

$usuario = $stmt->fetch();

if (!$usuario || !password_verify($password, $usuario['contrasena'])) {
    responder(401, null, 'Credenciales inválidas');
    }

if (!$usuario['activo']){
    responder(403, null, 'El usuario se encuentra desactivado');
}

        session_regenerate_id(true);

        $_SESSION['usuario_actual'] = [
            'id' => $usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'email' => $usuario['email'],
            'id_rol' => $usuario['id_rol'],
            'rol' => $usuario['nombre_rol']
        ];

        responder(200, $_SESSION['usuario_actual'], 'Login exitoso');
    

