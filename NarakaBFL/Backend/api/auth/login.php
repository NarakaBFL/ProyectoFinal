<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();

$email = strtolower(trim($body['email'] ?? ''));
$password = $body['password'] ?? '';

if ($email === '' || $password === '') {
    responder(400, null, 'Email y contraseña son obligatorios');
}

foreach ($_SESSION['usuarios'] as $usuario) {

    // Evita errores si quedó algún usuario viejo mal guardado.
    if (!isset($usuario['email'], $usuario['contrasena'])) {
        continue;
    }

    $emailCoincide = strtolower($usuario['email']) === $email;

    $contrasenaCoincide = password_verify(
        $password,
        $usuario['contrasena']
    );

    if ($emailCoincide && $contrasenaCoincide) {

        if (isset($usuario['activo']) && $usuario['activo'] === false) {
            responder(403, null, 'El usuario se encuentra desactivado');
        }

        session_regenerate_id(true);

        $_SESSION['usuario_actual'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'] ?? '',
            'email' => $usuario['email'],
            'id_rol' => $usuario['id_rol'],
            'rol' => $usuario['rol']
        ];

        responder(200, $_SESSION['usuario_actual'], 'Login exitoso');
    }
}

responder(401, null, 'Credenciales inválidas');
?>