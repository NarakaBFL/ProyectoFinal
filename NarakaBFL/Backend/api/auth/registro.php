<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../config/database.php';

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

$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->execute([$email]);

if($stmt->fetch()) {
    responder(409, null, 'El email ya está registrado');
}

//Cifrar contraseña 
$contrasenaHash = password_hash($password, PASSWORD_DEFAULT);

//El autorregistro siempre crea un Vecino (rol 1)
$stmt = $pdo->prepare("INSERT INTO usuario (nombre, apellido, email, contrasena, id_rol, activo) 
VALUES (?, ?, ?, ?, 1, 1)");

$stmt->execute([$nombre, $apellido, $email, $contrasenaHash]);

$nuevoId = $pdo->lastInsertId();

//No devolver la contraseña cifrada
$usuarioRespuesta = [
    'id' =>$nuevoId,
    'nombre' => $nombre,
    'apellido' => $apellido,
    'email' => $email,
    'id_rol' => 1,
    'rol' => 'Vecino',
    'activo' => true
];

responder(201, $usuarioRespuesta, 'Usuario registrado correctamente');
?>