<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();

$nombre = trim($body['nombre'] ?? '');
$apellido = trim($body['apellido'] ?? '');
$email = strtolower($body['email'] ?? '');
$password = $body['password'] ?? '';

if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
    responder(400, null, 'Faltan campos obligatorios');
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder(400, null, 'El email no tiene un formato válido');
}

if(strlen($password) < 8) {
    responder(400, null, 'La contraseña debe tener al menos 8 caracteres');
}

//Verificar que el email no esté registrado
foreach ($_SESSION['usuarios'] as $usuario) {
     if ( isset($usuario['email']) &&
        strtolower($usuario['email']) === $email) {
        responder(409, null, 'El email ya está registrado');
    }
}

if(!empty($_SESSION['usuarios'])) {
    $ids = array_column($_SESSION['usuarios'], 'id');

    $nuevoId = max($ids) +1;
}

//El autorregistro siempre crea un Vecino
$nuevoUsuario = [
    'id' => $nuevoId,
    'nombre' => $nombre,
    'apellido' => $apellido,
    'email' => $email,

    //El frontend envía "password", pero se guarda como "contrasena"
    'contrasena' => password_hash($password, PASSWORD_DEFAULT),
    'id_rol' => 1,
    'rol' => 'Vecino',
    'activo' => true
];

$_SESSION['usuarios'][] = $nuevoUsuario;

//No devolver la contraseña cifrada
$usuarioRespuesta = $nuevoUsuario;
unset($usuarioRespuesta['contrasena']);
responder(201, $usuarioRespuesta, 'Usuario registrado correctamente');

?>