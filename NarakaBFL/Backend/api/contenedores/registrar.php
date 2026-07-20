<?php
// registrar.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();
$ubicacion = $body['ubicacion'] ?? '';
$tipoResiduo = $body['tipo_residuo'] ?? '';
$capacidad = $body['capacidad'] ?? 0;

if (empty($ubicacion) || empty($tipoResiduo)) {
    responder(400, null, 'Faltan campos obligatorios');
}

$nuevoContenedor = [
    'id' => count($_SESSION['contenedores']) + 1,
    'ubicacion' => $ubicacion,
    'tipo_residuo' => $tipoResiduo,
    'capacidad' => $capacidad,
    'estado' => 'Activo'
];

$_SESSION['contenedores'][] = $nuevoContenedor;
responder(201, $nuevoContenedor, 'Contenedor registrado');