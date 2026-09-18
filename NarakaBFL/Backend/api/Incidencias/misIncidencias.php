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

if ((int) $_SESSION['usuario_actual']['id_rol'] !== 1) {
    responder(403, null, 'No tienes permiso para realizar esta acción');
}

$idUsuario = (int) $_SESSION['usuario_actual']['id'];

try {
     
    $stmt = $pdo->prepare("
    SELECT
        i.id_incidencia,
        i.tipo,
        i.estado,
        c.ubicacion AS calle
        FROM incidencia i
        INNER JOIN contenedor c
        ON i.id_contenedor = c.id_contenedor
        WHERE i.id_usuario = ?
        ORDER BY i.id_incidencia ASC");

        $stmt->execute([$idUsuario]);

        $incidencias = $stmt->fetchAll();

        responder(200, $incidencias, 'Listado de incidencias');

} catch(PDOException $e) {
    responder(500, null, 'Error al listar las incidencias');
}

?>