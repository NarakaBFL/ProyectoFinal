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
u.id_usuario,
u.nombre,
u.apellido,
u.email,
u.id_rol,
r.nombre_rol AS rol,
u.activo
FROM usuario u
INNER JOIN rol r
ON u.id_rol = r.id_rol
ORDER BY u.id_usuario ASC");

$stmt->execute();

$usuarios = $stmt->fetchAll();

responder(200, $usuarios, 'Listado de usuarios');

} catch (PDOException $e) {

    responder(500, null, 'Error al listar los usuarios');
}

?>