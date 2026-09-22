<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

if (!isset($_SESSION['usuario_actual'])) {
    responder(401, null, 'No hay una sesión activa');
}

if ((int) $_SESSION['usuario_actual']['id_rol'] !== 2) {
    responder(403, null, 'No tienes permiso para realizar esta acción');
}

$body = obtenerBody();

$nombre = trim($body['nombre'] ?? '');
$apellido = trim($body['apellido'] ?? '');
$email = trim($body['email'] ?? '');
$contrasena = $body['contrasena'] ?? '';
$idRol = (int) ($body['id_rol'] ?? 0);

if (empty($nombre) || empty($apellido) || empty($email) || empty($contrasena)){
responder(400, null, 'Nombre, apellido, email y constraseña con obligatorios');
}

if ($idRol <= 0) {
    responder(400, null, 'Debe seleccionar un rol');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder(400, null, 'El email no es válido');
}

try {

    // Verificar que el rol exista
    $stmt = $pdo->prepare("
    SELECT id_rol
    FROM rol
    WHERE id_rol = ?");

    $stmt->execute([$idRol]);

    if (!$stmt->fetch()) {
        responder(400, null, 'El rol seleccionado no existe');
    }

    //Verificar email duplicado
    $stmt = $pdo->prepare("
    SELECT id_usuario
    FROM usuario
    WHERE email = ?");

    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        responder(409, null, 'El email ya está registrado');
    }

    $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO usuario
        (
         nombre,
         apellido,
         email,
         contrasena,
         id_rol,
         activo
        )
        VALUES (?, ?, ?, ?, ?, 1)");

    $stmt->execute([
        $nombre,
        $apellido,
        $email,
        $contrasenaHash,
        $idRol
    ]);

    $nuevoUsuario = [
        'id_usuario' => (int) $pdo->lastInsertId(),
        'nombre' => $nombre,
        'apellido' => $apellido,
        'email' => $email,
        'id_rol' => $idRol,
        'activo' => 1
    ];

    responder(201, $nuevoUsuario, 'Usuario registrado correctamente');

} catch (PDOException $e) {

    responder(500, null, 'Error al registrar el usuario');
}

?>
