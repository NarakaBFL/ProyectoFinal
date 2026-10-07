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
    responder(403, null, 'No tiene permiso para realizar esta acción');
}

$datos = obtenerBody();

$idIncidencia = (int) ($datos['id_incidencia'] ?? 0);
$estado = trim($datos['estado'] ?? '');

//Estados permitidos
$estadosValidos = [
    'abierta',
    'en_curso',
    'resuelta'
];

if ($idIncidencia <= 0) {
    responder(400, null, 'ID de incidencia inválido');
}

if (!in_array($estado, $estadosValidos, true)) {
    responder(400, null, 'El estado de la incidencia no es válido');
}

try {

    //Verificar que la incidencia exista
    $stmt = $pdo->prepare("
    SELECT id_incidencia 
    FROM incidencia 
    WHERE id_incidencia = ?");

    $stmt->execute([$idIncidencia]);

    if (!$stmt->fetch()) {
        responder(404, null, 'Incidencia no encontrada');
    }

    //Actualizar estado
    $stmt = $pdo->prepare("
    UPDATE incidencia
    SET estado = ?
    WHERE id_incidencia = ?");

    $stmt->execute([$estado, $idIncidencia]);

    responder(200, [
        'id_incidencia' => $idIncidencia,
        'estado' => $estado],
        'Estado de la incidencia actualizado correctamente');

} catch (PDOException $e) {
    responder(500, null, 'Error al actualizar el estado de la incidencia');
}
?>