<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    responder(405, 'Método no permitido');
}

$body = leerBody();
$id_centro = $body['id_centro'] ?? null;

if (!$id_centro) {
    responder(400, 'Falta el id_centro a modificar');
}

$pdo = getConnection();

// Verificar que exista
$stmt = $pdo->prepare("SELECT * FROM centro_acopio WHERE id_centro = :id");
$stmt->execute([':id' => $id_centro]);
$centro = $stmt->fetch();

if (!$centro) {
    responder(404, 'Centro de acopio no encontrado');
}

// Campos editables (RF-18.2). Se mantienen los valores actuales si no vienen en el body.
$nombre          = trim($body['nombre'] ?? $centro['nombre']);
$ubicacion       = trim($body['ubicacion'] ?? $centro['ubicacion']);
$tipo_residuo    = trim($body['tipo_residuo'] ?? $centro['tipo_residuo']);
$capacidad_total = $body['capacidad_total'] ?? $centro['capacidad_total'];
$ocupacion_actual = $body['ocupacion_actual'] ?? $centro['ocupacion_actual']; // RF-19.2

if (!is_numeric($capacidad_total) || $capacidad_total <= 0) {
    responder(400, 'La capacidad total debe ser un número mayor a 0');
}
if (!is_numeric($ocupacion_actual) || $ocupacion_actual < 0) {
    responder(400, 'La ocupación actual debe ser un número mayor o igual a 0');
}

$sql = "UPDATE centro_acopio
        SET nombre = :nombre,
            ubicacion = :ubicacion,
            tipo_residuo = :tipo_residuo,
            capacidad_total = :capacidad_total,
            ocupacion_actual = :ocupacion_actual
        WHERE id_centro = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nombre'           => $nombre,
    ':ubicacion'        => $ubicacion,
    ':tipo_residuo'     => $tipo_residuo,
    ':capacidad_total'  => $capacidad_total,
    ':ocupacion_actual' => $ocupacion_actual,
    ':id'               => $id_centro,
]);

// RF-19.3 y RF-19.4: cálculo automático de porcentaje y alerta al superar 80%
$porcentaje = round(($ocupacion_actual / $capacidad_total) * 100, 2);
$alerta = $porcentaje >= 80;

responder(200, 'Centro de acopio actualizado correctamente', [
    'id_centro'            => (int)$id_centro,
    'porcentaje_ocupacion' => $porcentaje,
    'alerta_capacidad'     => $alerta
]);
