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

$tipoResiduo = trim($body['tipo_residuo'] ?? '');
$capacidad   = $body['capacidad'] ?? 0;
$ubicacion   = trim($body['ubicacion'] ?? '');
$estado      = trim($body['estado'] ?? 'funcional');

$estadosValidos = [
    'funcional',
    'roto',
    'desbordado'
];

if (empty($tipoResiduo) || empty($ubicacion)) {
    responder(400, null, 'Tipo de residuo, ubicación y dirección son obligatorios');
}

if (!is_numeric($capacidad) || (float) $capacidad <= 0) {
    responder(400, null, 'La capacidad debe ser un número mayor que cero');
}

if (!in_array($estado, $estadosValidos, true)) {
    responder(400, null, 'El estado debe ser funcional, roto o desbordado');
}

try {

    $stmt = $pdo->prepare("
    INSERT INTO contenedor
    (tipo_residuo, capacidad, ubicacion, estado)
    VALUES (?, ?, ?, ?)");

    $stmt->execute([
        $tipoResiduo,
        (float) $capacidad,
        $ubicacion,
        $estado]);

    $nuevoContenedor = [
        'id_contenedor' => (int) $pdo->lastInsertId(),
        'tipo_residuo'  => $tipoResiduo,
        'capacidad'     => (float) $capacidad,
        'ubicacion'     => $ubicacion,
        'estado'        => $estado
    ];

    responder(201, $nuevoContenedor, 'Contenedor registrado correctamente');

} catch (PDOException $e) {
    responder(500, null, 'Error al registrar el contenedor');
}

?>