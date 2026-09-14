<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, 'Método no permitido');
}

$body = leerBody();

// RF-18.1: nombre, ubicación, capacidad total y tipo de residuos gestionados
$nombre          = trim($body['nombre'] ?? '');
$ubicacion       = trim($body['ubicacion'] ?? '');
$tipo_residuo    = trim($body['tipo_residuo'] ?? '');
$capacidad_total = $body['capacidad_total'] ?? null;

// Validación de campos obligatorios
if ($nombre === '' || $ubicacion === '' || $tipo_residuo === '' || $capacidad_total === null) {
    responder(400, 'Faltan campos obligatorios: nombre, ubicacion, tipo_residuo, capacidad_total');
}

if (!is_numeric($capacidad_total) || $capacidad_total <= 0) {
    responder(400, 'La capacidad total debe ser un número mayor a 0');
}

$pdo = getConnection();

$sql = "INSERT INTO centro_acopio (nombre, ubicacion, tipo_residuo, capacidad_total, ocupacion_actual, estado)
        VALUES (:nombre, :ubicacion, :tipo_residuo, :capacidad_total, 0, 'Activo')";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nombre'          => $nombre,
    ':ubicacion'       => $ubicacion,
    ':tipo_residuo'    => $tipo_residuo,
    ':capacidad_total' => $capacidad_total,
]);

responder(201, 'Centro de acopio registrado correctamente', ['id_centro' => $pdo->lastInsertId()]);
