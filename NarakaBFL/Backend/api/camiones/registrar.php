<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();
$fecha = $body['fecha'] ?? '';
$matricula = $body['matricula'] ?? '';
$marca = $body['marca'] ?? '';
$modelo = $body['modelo'] ?? '';
$carga = $body['carga'] ?? 0;
$estado = $body['estado'] ?? '';
$chofer = $body['chofer'] ?? '';

if (empty($matricula) || empty($marca) || empty($modelo) || empty($estado)) {
    responder(400, null, 'Faltan campos obligatorios');
}

$nuevoCamion = [
    'id' => count($_SESSION['camiones']) + 1,
    'fecha' => $fecha,
    'matricula' => $matricula,
    'marca' => $marca,
    'modelo' => $modelo,
    'carga' => $carga,
    'estado' => $estado,
    'chofer' => $chofer
];

$_SESSION['camiones'][] = $nuevoCamion;
responder(201, $nuevoCamion, 'Camión registrado');
