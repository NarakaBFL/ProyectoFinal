<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
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

$idCamion = $body['id_camion'] ?? 0;

if (!is_numeric($idCamion) || (int) $idCamion <= 0) {
    responder(400, null, 'ID de camión inválido');
}

//Comprobar que exista
$stmt = $pdo->prepare("
SELECT id_camion
FROM camion
WHERE id_camion = ?");

$stmt->execute([$idCamion]);

if (!$stmt->fetch()) {
    responder(404, null, 'Camion no encontrado');
}

try {

$stmt = $pdo->prepare("
DELETE FROM camion
WHERE id_camion = ?");

$stmt->execute([$idCamion]);

responder(200, 
['id_camion' => (int) $idCamion],
'Camion eliminado correctamente');

} catch (PDOException $e) {

    responder(409, null, 'No se puede eliminar el camión porque está siendo utilizado por otro resgistro');
}
?>