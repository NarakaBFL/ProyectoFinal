<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();
$id = $body['id_contenedor'] ?? null;

if (empty($id) || !is_numeric($id)) {
    responder(400, null, 'Falta el id_contenedor a eliminar');
}

try {
    $stmt = $pdo->prepare("SELECT id_contenedor FROM contenedor WHERE id_contenedor = :id AND activo = 1");
    $stmt->execute([':id' => $id]);

    if (!$stmt->fetch()) {
        responder(404, null, 'Contenedor no encontrado');
    }

    // Baja lógica (RF-06.5): se conserva el registro para trazabilidad histórica
    $stmt = $pdo->prepare("UPDATE contenedor SET activo = 0 WHERE id_contenedor = :id");
    $stmt->execute([':id' => $id]);

    responder(200, null, 'Contenedor dado de baja correctamente');
} catch (PDOException $e) {
    responder(500, null, 'Error al eliminar el contenedor');
}
?>
