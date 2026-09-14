<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(405, 'Método no permitido');
}

$pdo = getConnection();

// Filtros opcionales (RF-18.4: filtrable por tipo de residuo y/o ubicación)
$tipo_residuo = $_GET['tipo_residuo'] ?? null;
$ubicacion    = $_GET['ubicacion'] ?? null;
$estado       = $_GET['estado'] ?? null;

$sql = "SELECT id_centro, nombre, ubicacion, tipo_residuo, capacidad_total,
               ocupacion_actual,
               ROUND((ocupacion_actual / capacidad_total) * 100, 2) AS porcentaje_ocupacion,
               estado, fecha_alta
        FROM centro_acopio
        WHERE 1=1";
$params = [];

if ($tipo_residuo) {
    $sql .= " AND tipo_residuo = :tipo_residuo";
    $params[':tipo_residuo'] = $tipo_residuo;
}
if ($ubicacion) {
    $sql .= " AND ubicacion LIKE :ubicacion";
    $params[':ubicacion'] = "%$ubicacion%";
}
if ($estado) {
    $sql .= " AND estado = :estado";
    $params[':estado'] = $estado;
}

$sql .= " ORDER BY nombre ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$centros = $stmt->fetchAll();

responder(200, 'Listado de centros de acopio y vertederos', $centros);
