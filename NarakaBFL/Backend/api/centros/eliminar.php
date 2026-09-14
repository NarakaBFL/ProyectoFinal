<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    responder(405, 'Método no permitido');
}

$body = leerBody();
$id_centro = $body['id_centro'] ?? null;

if (!$id_centro) {
    responder(400, 'Falta el id_centro a eliminar');
}

$pdo = getConnection();

$stmt = $pdo->prepare("SELECT id_centro FROM centro_acopio WHERE id_centro = :id");
$stmt->execute([':id' => $id_centro]);

if (!$stmt->fetch()) {
    responder(404, 'Centro de acopio no encontrado');
}

// Baja lógica: se conserva el registro para trazabilidad histórica,
// igual criterio que RF-04.3 (baja lógica de usuarios)
$stmt = $pdo->prepare("UPDATE centro_acopio SET estado = 'Inactivo' WHERE id_centro = :id");
$stmt->execute([':id' => $id_centro]);

responder(200, 'Centro de acopio dado de baja correctamente');
