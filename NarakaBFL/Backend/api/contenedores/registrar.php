<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();
$codigo = $body['codigo'] ?? '';
$tipo = $body['tipo'] ?? '';
$litros = $body['litros'] ?? 0;
$ubi = $body['ubi'] ?? '';
$estado = $body['estado'] ?? '';

if (empty($codigo) || empty($tipo) || empty($ubi) || empty($estado)) {
    responder(400, null, 'Faltan campos obligatorios');
}

$nuevoContenedor = [
    'id' => count($_SESSION['contenedores']) + 1,
    'codigo' => $codigo,
    'tipo' => $tipo,
    'litros' => $litros,
    'ubi' => $ubi,
    'estado' => $estado
];

$_SESSION['contenedores'][] = $nuevoContenedor;
responder(201, $nuevoContenedor, 'Contenedor registrado');
