<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(405, null, 'Método no permitido');
}

$camiones = $_SESSION['camiones'] ?? [];

responder(200, $camiones, 'Listado de camiones');