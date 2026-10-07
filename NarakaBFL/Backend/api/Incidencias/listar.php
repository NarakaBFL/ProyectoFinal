<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(405, null, 'Método no permitido');
}

if (!isset($_SESSION['usuario_actual'])) {
    responder(401, null, 'No hay una sesión activa');
}

//Solo Administrador
if ((int) $_SESSION['usuario_actual']['id_rol'] !== 2) {
    responder(403, null, 'No tienes permiso para realizar esta acción');
}

try {

    $stmt = $pdo->prepare("
    SELECT
    i.id_incidencia,
    i.fecha,
    i.tipo,
    i.descripcion,
    i.estado,
    i.id_contenedor,
    c.ubicacion AS contenedor,
    i.id_usuario,
    CONCAT(u.nombre, ' ', u.apellido) AS usuario
    FROM incidencia i

    INNER JOIN contenedor c
    ON i.id_contenedor = c.id_contenedor

    INNER JOIN usuario u
    ON i.id_usuario = u.id_usuario

    ORDER BY i.id_incidencia ASC
    ");

    $stmt->execute();

    $incidencias = $stmt->fetchAll();

    responder(200, $incidencias, 'Listado de incidencias');

} catch (PDOException $e) {

    responder(500, null, 'Error al listar las incidencias');
}

?>