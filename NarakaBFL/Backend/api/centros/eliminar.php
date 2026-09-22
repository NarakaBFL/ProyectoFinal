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

$id = (int) ($body['id_centro'] ?? 0);

if ($id <= 0) {
    responder(400, null, 'Falta el id_centro a eliminar');
}

try {

$stmt = $pdo->prepare("
SELECT id_centro 
FROM centro_acopio 
WHERE id_centro = ?");

$stmt->execute([$id]);

if (!$stmt->fetch()) {
    responder(404, null, 'Centro de acopio no encontrado');
}

// Eliminar el centro
$stmt = $pdo->prepare("
DELETE FROM centro_acopio
WHERE id_centro = ?");

$stmt->execute([$id]);

responder(200, null, 'Centro de acopio eliminado correctamente');

} catch (PDOException $e) {

if ($e->getCode() === '23000') {
    responder(409, null, 'No se puede eliminar el centro porque tiene datos asociados');
}

responder(500, null, 'Error al eliminar el centro de acopio');

}

?>