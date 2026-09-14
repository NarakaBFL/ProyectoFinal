<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(405, null, 'Método no permitido');
}

// Filtros opcionales: RF-08.2 (zona/ubicación), RF-08.3 (tipo de residuo), RF-08.4 (estado)
$ubicacion    = $_GET['ubicacion'] ?? null;
$tipo_residuo = $_GET['tipo_residuo'] ?? null;
$estado       = $_GET['estado'] ?? null;

$sql = "SELECT id_contenedor, tipo_residuo, capacidad, ubicacion, direccion, estado, fecha_alta
        FROM contenedor
        WHERE activo = 1";
$params = [];

if ($ubicacion) {
    $sql .= " AND ubicacion LIKE :ubicacion";
    $params[':ubicacion'] = "%$ubicacion%";
}
if ($tipo_residuo) {
    $sql .= " AND tipo_residuo = :tipo_residuo";
    $params[':tipo_residuo'] = $tipo_residuo;
}
if ($estado) {
    $sql .= " AND estado = :estado";
    $params[':estado'] = $estado;
}

$sql .= " ORDER BY id_contenedor ASC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $contenedores = $stmt->fetchAll();

    responder(200, $contenedores, 'Listado de contenedores');
} catch (PDOException $e) {
    responder(500, null, 'Error al listar los contenedores');
}
?>
