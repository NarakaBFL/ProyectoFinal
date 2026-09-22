<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    responder(405, null, 'Método no permitido');
}

if (!isset($_SESSION['usuario_actual'])) {
    responder(401, null, 'No hay una sesión activa');
}

if ((int) $_SESSION['usuario_actual']['id_rol'] !== 2) {
    responder(403, null, 'No tienes permiso para realizar esta acción');
}

$body = obtenerBody();

$id = (int) ($body['id_usuario'] ?? 0);

if ($id <= 0) {
    responder(400, null, 'Falta el id_usuario a modificar');
}

try {

    // Verificar que el usuario exista
    $stmt = $pdo->prepare("
        SELECT *
        FROM usuario
        WHERE id_usuario = ?
    ");

    $stmt->execute([$id]);

    $actual = $stmt->fetch();

    if (!$actual) {
        responder(404, null, 'Usuario no encontrado');
    }

    // Mantener datos actuales si no vienen en el body
    $nombre = trim($body['nombre'] ?? $actual['nombre']);
    $apellido = trim($body['apellido'] ?? $actual['apellido']);
    $email = trim($body['email'] ?? $actual['email']);
    $idRol = (int) ($body['id_rol'] ?? $actual['id_rol']);
    $activo = isset($body['activo'])
        ? (int) $body['activo']
        : (int) $actual['activo'];

    $contrasena = $body['contrasena'] ?? '';

    if (empty($nombre) || empty($apellido) || empty($email)) {
        responder(400, null, 'Nombre, apellido y email son obligatorios');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        responder(400, null, 'El email no es válido');
    }

    if ($activo !== 0 && $activo !== 1) {
        responder(400, null, 'El estado del usuario no es válido');
    }

    // Verificar que el rol exista
    $stmt = $pdo->prepare("
        SELECT id_rol
        FROM rol
        WHERE id_rol = ?
    ");

    $stmt->execute([$idRol]);

    if (!$stmt->fetch()) {
        responder(400, null, 'El rol seleccionado no existe');
    }

    // Verificar que el email no lo use otro usuario
    $stmt = $pdo->prepare("
        SELECT id_usuario
        FROM usuario
        WHERE email = ?
        AND id_usuario <> ?
    ");

    $stmt->execute([
        $email,
        $id
    ]);

    if ($stmt->fetch()) {
        responder(409, null, 'El email ya está registrado');
    }

    // Si se ingresó una contraseña nueva, actualizarla
    if (!empty($contrasena)) {

        $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE usuario
            SET nombre = ?,
                apellido = ?,
                email = ?,
                contrasena = ?,
                id_rol = ?,
                activo = ?
            WHERE id_usuario = ?
        ");

        $stmt->execute([
            $nombre,
            $apellido,
            $email,
            $contrasenaHash,
            $idRol,
            $activo,
            $id
        ]);

    } else {

        $stmt = $pdo->prepare("
            UPDATE usuario
            SET nombre = ?,
                apellido = ?,
                email = ?,
                id_rol = ?,
                activo = ?
            WHERE id_usuario = ?
        ");

        $stmt->execute([
            $nombre,
            $apellido,
            $email,
            $idRol,
            $activo,
            $id
        ]);
    }

    $usuarioActualizado = [
        'id_usuario' => $id,
        'nombre' => $nombre,
        'apellido' => $apellido,
        'email' => $email,
        'id_rol' => $idRol,
        'activo' => $activo
    ];

    responder(200, $usuarioActualizado, 'Usuario actualizado correctamente');

} catch (PDOException $e) {

    responder(
        500,
        null,
        'Error al modificar el usuario'
    );
}

?>