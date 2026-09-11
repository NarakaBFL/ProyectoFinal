<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(405, null, 'Método no permitido');
}

if (!isset($_SESSION['usuario_actual'])) {
    responder(401, null, 'No hay una sesión activa');
}

if ((int) $_SESSION['usuario_actual']['id_rol'] !== 2) {
    responder(403, null, 'No tienes permiso para realizar esta acción');
}

$stmt = $pdo->query("SELECT
id_camion, matricula, estado, capacidad, id_usuario_asigna
FROM camion 
ORDER BY id_camion DESC");

$camiones = $stmt->fetchAll();

responder(200, $camiones, 'Listado de camiones');

?>