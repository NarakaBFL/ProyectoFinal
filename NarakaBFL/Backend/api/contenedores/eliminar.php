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

$id = (int) ($body['id_contenedor'] ?? 0);

if ($id <= 0) {
    responder(400, null, 'Falta el id_contenedor a eliminar');
}

try {
    $stmt = $pdo->prepare("
    SELECT id_contenedor 
    FROM contenedor 
    WHERE id_contenedor = ?");

    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
        responder(404, null, 'Contenedor no encontrado');
    }

    $stmt = $pdo->prepare("
    DELETE FROM contenedor
    WHERE id_contenedor = ?");

    $stmt->execute([$id]);

    responder(200, null, 'Contenedor eliminado correctamente');

} catch (PDOException $e) {

    responder(500, null, 'Error al eliminar el contenedor');
}
?>