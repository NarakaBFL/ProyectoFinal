<?php
// listar.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(405, null, 'Método no permitido');
}

responder(200, $_SESSION['contenedores'], 'Listado de contenedores');
