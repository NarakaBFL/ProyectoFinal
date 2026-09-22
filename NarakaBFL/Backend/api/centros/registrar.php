<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

if (!isset($_SESSION['usuario_actual'])) {
    responder(401, null, 'No hay una sesión activa');
}

if ((int) $_SESSION['usuario_actual']['id_rol'] !== 2) {
    responder(403, null, 'No tienes permiso para realizar esta acción');
}

$body = obtenerBody();

$nombre = trim($body['nombre'] ?? '');
$ubicacion = trim($body['ubicacion'] ?? '');
$tipoResiduo = trim($body['tipo_residuo'] ?? '');
$capacidadTotal = $body['capacidad_total'] ?? 0;

// Validación de campos obligatorios
if (empty($nombre) || empty($ubicacion) || empty($tipoResiduo)) {
    responder(400, null, 'Nombre, ubicacion y tipo de residuo son obligatorios');
}

if (!is_numeric($capacidadTotal) || (float) $capacidadTotal <= 0) {
    responder(400, null, 'La capacidad total debe ser un número mayor a 0');
}
 try {

$stmt = $pdo->prepare("
INSERT INTO centro_acopio
(nombre, 
ubicacion,
tipo_residuo,
capacidad_total,
ocupacion_actual)
VALUES (?, ? ,?, ?, 0)");

$stmt->execute([
    $nombre,
    $ubicacion,
    $tipoResiduo,
    (float)$capacidadTotal,
]);

$nuevoCentro = [
    'id_centro' => (int) $pdo->lastInsertId(), 
    'nombre' => $nombre,
    'ubicacion' => $ubicacion,
    'tipo_residuo' => $tipoResiduo,
    'capacidad_total' => (float) $capacidadTotal,
    'ocupacion_actual' => 0
];

responder(201, $nuevoCentro, 'Centro de acopio registrado correctamente');

 } catch (PDOException $e) {
    responder(500, null, 'Error al registrar el centro de acopio');
 }
?>