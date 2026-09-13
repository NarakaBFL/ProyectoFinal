<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    responder(405, null, 'Método no permitido');
}

if (!isset($_SESSION['usuario_actual'])) {
    responder(401, null, 'No hay una sesión activa');
}

if ((int) $_SESSION['usuario_actual']['id_rol'] !== 2) {
    responder(403, null, 'No tienes permiso para realizar esta acción');
}

$datos = obtenerBody();

$idMaquinaria = (int) ($datos['id_maquinaria'] ?? 0);

if ($idMaquinaria <= 0) {
    responder(400, null, 'ID de la maquinaria es obligatorio');
}

//Comprobar que exista
$stmt = $pdo->prepare("
SELECT id_maquinaria
FROM maquinaria
WHERE id_maquinaria = ?");

$stmt->execute([$idMaquinaria]);

if (!$stmt->fetch()) {
    responder(404, null, 'La maquinaria no existe');
}

//Eliminar
try {

    $stmt = $pdo->prepare("
DELETE FROM maquinaria
WHERE id_maquinaria = ?");

    $stmt->execute([$idMaquinaria]);

    responder(200, null, 'Maquinaria eliminada correctamente');

} catch (PDOException $e) {

    responder(409, null, 'No se puede eliminar la maquinaria porque está siendo utilizada');
}
?>