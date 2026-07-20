<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(405, null, 'Método no permitido');
}

$usuarios = array_map(function ($u) {
    unset($u['password']);
    return $u;
}, $_SESSION['usuarios']);

responder(200, $usuarios, 'Listado de usuarios');
