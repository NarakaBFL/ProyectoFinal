<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(405, null, 'Método no permitido');
}

$usuarios = $_SESSION['usuarios'];

foreach ($usuarios as &$usuario){
    unset(
        $usuario['contrasena'],
        $usuario['password']
    );
}

unset($usuario);

responder(200, $usuarios, 'Listado de usuarios');