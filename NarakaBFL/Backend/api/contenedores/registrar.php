<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();

$tipoResiduo = trim($body['tipo_residuo'] ?? '');
$capacidad = $body['capacidad'] ?? 0;
$ubicacion = trim($body['ubicacion'] ?? '');
$direccion = trim($body['direccion'] ?? '');
$estado = trim($body['estado'] ?? 'funcional');

$estadosValidos = [
    'funcional',
    'roto',
    'desbordado'
];

if (empty($tipoResiduo) || empty($ubicacion) || empty($direccion)) {
    responder(400, null, 'Tipo de residuo, ubicación y dirección son obligatorios');
}

if (!is_numeric($capacidad) || (float) $capacidad <= 0) {
    responder(400, null, 'La capacidad debe ser un número mayor que cero');
}

if (!in_array($estado, $estadosValidos, true)) {
    responder(400, null, 'El estado debe ser funcional, roto o desbordado');
}

$nuevoId = 1;

if (!empty($_SESSION['contenedores'])) {
    $ids = array_column(
        $_SESSION['contenedores'],
        'id_contenedor'
    );

    $nuevoId = max($ids) + 1;
}

$nuevoContenedor = [
    'id_contenedor' => $nuevoId,
    'estado' => $estado,
    'tipo_residuo' => $tipoResiduo,
    'capacidad' => (float) $capacidad,
    'ubicacion' => $ubicacion,
    'direccion' => $direccion
];

$_SESSION['contenedores'][] = $nuevoContenedor;

responder(201, $nuevoContenedor, 'Contenedor registrado correctamente');
?>