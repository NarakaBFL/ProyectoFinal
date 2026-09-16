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
    $stmt = $pdo->prepare("SELECT id_contenedor,
    tipo_residuo, capacidad, ubicacion, estado
    FROM contenedor
    ORDER BY id_contenedor ASC
    ");

$stmt->execute();

$contenedores = $stmt->fetchAll();

responder(200, $contenedores, 'Listado de contenedores');

} catch (PDOException $e) {
    responder(500, null, 'Error al listar los contenedores');
}
?>