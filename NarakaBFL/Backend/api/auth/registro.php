<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();
$nombre = $body['nombre'] ?? '';
$apellido = $body['apellido'] ?? '';
$email = $body['email'] ?? '';
$password = $body['password'] ?? '';
$rol = 'Vecino'; // autorregistro siempre entra como Vecino

if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
    responder(400, null, 'Faltan campos obligatorios');
}

foreach ($_SESSION['usuarios'] as $usuario) {
    if ($usuario['email'] === $email) {
        responder(409, null, 'El email ya está registrado');
    }
}

$nuevoId = count($_SESSION['usuarios']) + 1;
$nuevoUsuario = [
    'id' => $nuevoId,
    'nombre' => $nombre,
    'apellido' => $apellido,
    'email' => $email,
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'rol' => $rol
];

$_SESSION['usuarios'][] = $nuevoUsuario;

unset($nuevoUsuario['password']);
responder(201, $nuevoUsuario, 'Usuario registrado correctamente');
