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

if ((int) $_SESSION['usuario_actual']['id_rol'] !== 2) {
    responder(403, null, 'No tienes permiso para realizar esta acción');
}

try {

    $stmt = $pdo->prepare("
    SELECT
        r.id_ruta,
        r.num_ruta,
        r.zona,
        r.recorrido,
        r.id_camion,
        c.matricula AS camion,
        r.id_usuario_cuadrilla,
        CONCAT(u.nombre, ' ', u.apellido) AS cuadrilla
        FROM ruta r
        INNER JOIN camion c
            ON r.id_camion = c.id_camion
        INNER JOIN usuario u
            ON r.id_usuario_cuadrilla = u.id_usuario
        ORDER BY r.num_ruta ASC");

        $stmt->execute();

        $rutas = $stmt->fetchAll();

        responder(200, $rutas, 'Listado de rutas');

}catch (PDOException $e) {

    responder(500, null, 'Error al listar las rutas');
}
?>