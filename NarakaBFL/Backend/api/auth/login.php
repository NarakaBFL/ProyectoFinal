<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();
$email = $body['email'] ?? '';
$password = $body['password'] ?? '';

if (empty($email) || empty($password)) {
    responder(400, null, 'Email y contraseña son obligatorios');
}

foreach ($_SESSION['usuarios'] as $usuario) {
    if ($usuario['email'] === $email && password_verify($password, $usuario['password'])) {
        $_SESSION['usuario_actual'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol']
        ];
        responder(200, $_SESSION['usuario_actual'], 'Login exitoso');
    }
}

responder(401, null, 'Credenciales inválidas');