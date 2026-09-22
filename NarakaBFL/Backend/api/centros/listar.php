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
    id_centro, 
    nombre,
    ubicacion,
    tipo_residuo,
    capacidad_total,
    ocupacion_actual
    FROM centro_acopio
    ORDER BY id_centro ASC");

    $stmt->execute();

    $centros = $stmt->fetchAll();

    responder(200, $centros, 'Listado de centros de acopio');

} catch (PDOException $e) {
responder(500, null, 'Error al listar los centros de acopio');
}

?>
