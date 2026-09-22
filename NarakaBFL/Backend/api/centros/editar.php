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

$id = (int) ($body['id_centro'] ?? 0);

if ($id <= 0) {
    responder(400, null, 'Falta el id_centro a modificar');
}

try{

// Verificar que el centro exista
$stmt = $pdo->prepare("
SELECT * 
FROM centro_acopio 
WHERE id_centro = ?");

$stmt->execute([$id]);

$actual = $stmt->fetch();

if (!$actual) {
    responder(404, 'Centro de acopio no encontrado');
}

$nombre = trim($body['nombre'] ?? $actual['nombre']);
$ubicacion = trim($body['ubicacion'] ?? $actual['ubicacion']);
$tipoResiduo = trim($body['tipo_residuo'] ?? $actual['tipo_residuo']);
$capacidadTotal = $body['capacidad_total'] ?? $actual['capacidad_total'];
$ocupacionActual = $body['ocupacion_actual'] ?? $actual['ocupacion_actual']; // RF-19.2

if (empty($nombre) || empty($ubicacion) || empty($tipoResiduo)) {
    responder(400, null, 'Nombre, ubicación y tipo residuo son obligatorios');
}

if (!is_numeric($capacidadTotal) || (float) $capacidadTotal <= 0) {
    responder(400, null, 'La capacidad total debe ser un número mayor o igual a 0');
}

if (!is_numeric($ocupacionActual) || (float) $ocupacionActual < 0) {
    responder(400, null, 'La ocupación actual debe ser un número mayor o igual a 0');
}

$stmt = $pdo->prepare("
UPDATE centro_acopio
SET nombre = ?,
ubicacion = ?,
tipo_residuo = ?,
capacidad_total = ?, 
ocupacion_actual = ?
WHERE id_centro = ?");

$stmt->execute([
$nombre,
$ubicacion,
$tipoResiduo,
(float) $capacidadTotal,
(float) $ocupacionActual,
$id]);

$porcentaje = round(( (float) $ocupacionActual / (float) $capacidadTotal) * 100, 2);

$alerta = $porcentaje >= 80;

$centroActualizado = [
    'id_centro' => $id, 
    'nombre' => $nombre, 
    'ubicacion' => $ubicacion, 
    'tipo_residuo' => $tipoResiduo, 
    'capacidad_total' => (float) $capacidadTotal, 
    'ocupacion_actual' => (float) $ocupacionActual,
    'porcentaje_ocupacion' => $porcentaje, 
    'alerta_capacidad' => $alerta
];

responder(200, $centroActualizado, 'Centro de acopio actualizado correctamente');

} catch (PDOException $e) {
    responder(500, null, 'Error al modificar el centro de acopio');
}

?>