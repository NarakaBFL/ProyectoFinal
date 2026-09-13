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
m.id_maquinaria,
m.tipo,
m.estado,
m.fecha_ultimo_mantenimiento,
m.id_centro,
c.nombre AS centro
FROM maquinaria m
INNER JOIN centro_acopio c
ON m.id_centro = c.id_centro
ORDER BY m.id_maquinaria DESC");

$maquinaria = $stmt->fetchAll();

responder(200, $maquinaria, 'Listado de maquinaria');

?>