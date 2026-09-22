<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
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
    responder(400, null, 'Falta el id_usuario a eliminar');
}

try {

    $stmt = $pdo->prepare("
        SELECT id_usuario, activo
        FROM usuario
        WHERE id_usuario = ?
    ");

    $stmt->execute([$id]);

    $usuario = $stmt->fetch();

    if (!$usuario) {
        responder(404, null, 'Usuario no encontrado');
    }

    if ((int) $usuario['activo'] === 0) {
        responder(400, null, 'El usuario ya se encuentra inactivo');
    }

    $stmt = $pdo->prepare("
        UPDATE usuario
        SET activo = 0
        WHERE id_usuario = ?
    ");

    $stmt->execute([$id]);

    responder(200, null, 'Usuario dado de baja correctamente');

} catch (PDOException $e) {

    responder(500, null, 'Error al eliminar el usuario');
}

?>