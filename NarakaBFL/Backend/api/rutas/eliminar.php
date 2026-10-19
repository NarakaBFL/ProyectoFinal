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

$idRuta = (int) ($body['id_ruta'] ?? 0);

if ($idRuta <=0) {
    responder(400, null, 'Falta el id_ruta a eliminar');
}

try {

//Verificar que la ruta existe
$stmt = $pdo->prepare("
SELECT id_ruta
FROM ruta
WHERE id_ruta = ?");

$stmt->execute([$idRuta]);

if (!$stmt->fetch()) {
    responder(404, null, 'Ruta no encontrada');
}

//Eliminar ruta 
$stmt = $pdo->prepare("
DELETE FROM ruta
WHERE id_ruta = ?");

$stmt->execute([$idRuta]);

responder(200, null, 'Ruta eliminada correctamente');


}catch (PDOException $e) {
    responder(500, null, 'Error al eliminar la ruta');
}
?>