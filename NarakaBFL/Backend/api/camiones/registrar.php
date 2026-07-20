<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();
$patente = $body['patente'] ?? '';
$modelo = $body['modelo'] ?? '';
$capacidadCarga = $body['capacidad_carga'] ?? 0;

if (empty($patente) || empty($modelo)) {
    responder(400, null, 'Faltan campos obligatorios');
}

$nuevoCamion = [
    'id' => count($_SESSION['camiones']) + 1,
    'patente' => $patente,
    'modelo' => $modelo,
    'capacidad_carga' => $capacidadCarga,
    'estado' => 'Activo'
];

$_SESSION['camiones'][] = $nuevoCamion;
responder(201, $nuevoCamion, 'Camión registrado');
