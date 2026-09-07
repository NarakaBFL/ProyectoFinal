<?php 

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder (405, null, 'Método no permitido');
}

if (!isset($_SESSION['usuario_actual'])) {
    responder(401, null, 'No hay usuario logueado');
}

responder(200, $_SESSION['usuario_actual'], 'Sesión activa');

?>