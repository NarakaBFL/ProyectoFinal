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

$datos = obtenerBody();

$tipo = trim($datos['tipo'] ?? '');
$estado = trim($datos['estado'] ?? 'disponible');
$fechaUltimoMantenimiento = $datos['fecha_ultimo_mantenimiento'] ?? null;
$idCentro = (int) ($datos['id_centro'] ?? 0);

$estadosValidos = [
    'disponible',
    'en_uso',
    'mantenimiento',
    'fuera_de_servicio'
];

if (empty($tipo)) {
    responder(400, null, 'El tipo de maquinaria es obligatorio');
}

if (!in_array($estado, $estadosValidos, true)) {
    responder(400, null, 'El estado ingresado no es válido');
}

if ($idCentro <= 0) {
    responder(400, null, 'El centro de acopio es obligatorio');
}

// Si la fecha tiene vacía, se guarda como NULL 
if (empty($fechaUltimoMantenimiento)) {
    $fechaUltimoMantenimiento = null;
}

// Verificar que el centro exista
$stmt = $pdo->prepare("
SELECT id_centro
FROM centro_acopio
WHERE id_centro = ?");

$stmt->execute([$idCentro]);

if (!$stmt->fetch()) {
    responder(409, null, 'El centro de acopio no existe');
}

// Registrar maquinaria 
$stmt = $pdo->prepare("
INSERT INTO maquinaria (tipo, estado, fecha_ultimo_mantenimiento, id_centro)
VALUES (?, ?, ?, ?)");

$stmt->execute([
    $tipo,
    $estado,
    $fechaUltimoMantenimiento,
    $idCentro
]);

responder(201, ['id_maquinaria' => $pdo->lastInsertId()], 'Maquinaria registrada correctamente');