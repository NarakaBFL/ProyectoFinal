<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
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

$idCamion = $body['id_camion'] ?? 0;
$matricula = strtoupper(trim($body['matricula'] ?? ''));
$estado = trim($body['estado'] ?? '');
$capacidad = $body['capacidad'] ?? 0;

$estadosValidos = ['disponible', 'en_servicio', 'mantenimiento', 'fuera_de_servicio'];

if (!is_numeric($idCamion) || (int) $idCamion <= 0) {
    responder(400, null, 'ID de camión inválido');
} 

if ($matricula === '') {
    responder(400, null, 'La matricula es obligatoria');
}

if (!is_numeric($capacidad) || (float) $capacidad <= 0) {
    responder(400, null, 'La capacidad debe ser mayor que cero');
}

if (!in_array($estado, $estadosValidos, true)){
    responder(400, null, 'El estado del camión no es válido');
}

//Comprobar que el camión exista
$stmt = $pdo->prepare("
SELECT id_camion
FROM camion
WHERE id_camion = ?");

$stmt->execute([$idCamion]);

if (!$stmt->fetch()) {
    responder(404, null, 'Camión no encontrado');
}

// Comprobar que la matrícula no pertenezca a otro camión
$stmt = $pdo->prepare("
SELECT id_camion
FROM camion
WHERE matricula = ?
AND id_camion <> ?");

$stmt->execute([$matricula, $idCamion]);

if ($stmt->fetch()) {
    responder(409, null, 'La matrícula ya está registrada');
}

//Actualizar
$stmt = $pdo->prepare("
UPDATE camion
SET matricula = ?,
estado = ?,
capacidad = ?
WHERE id_camion = ?");

$stmt->execute([
    $matricula,
    $estado,
    $capacidad,
    $idCamion
]);

responder(200,[
    'id_camion' => (int) $idCamion,
    'matricula' => $matricula,
    'estado' => $estado,
    'capacidad' => (float) $capacidad
],
'Camion actualizado correctamente');

?>