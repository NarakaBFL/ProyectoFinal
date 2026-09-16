<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    responder(405, null, 'Método no permitido');
}

if (!isset($_SESSION['usuario_actual'])) {
    responder(401, null, 'No hay una sesión activa');
}

if ((int) $_SESSION['usuario_actual']['id_rol'] !== 2) {
    responder(403, null, 'No tienes permiso para realizar esta acción');
}

$body = obtenerBody();

$id = (int) ($body['id_contenedor'] ?? 0);

if ($id <= 0) {
    responder(400, null, 'Falta el id_contenedor a modificar');
}

$estadosValidos = [
    'funcional',
    'roto',
    'desbordado'
];

try {
    // Verificar que el contenedor exista 
    $stmt = $pdo->prepare("
        SELECT *
        FROM contenedor
        WHERE id_contenedor = ?");

    $stmt->execute([$id]);

    $actual = $stmt->fetch();

    if (!$actual) {
        responder(404, null, 'Contenedor no encontrado');
    }

    $tipoResiduo = trim($body['tipo_residuo'] ?? $actual['tipo_residuo']);
    $capacidad = $body['capacidad'] ?? $actual['capacidad'];
    $ubicacion = trim($body['ubicacion'] ?? $actual['ubicacion']);
    $estado = trim($body['estado'] ?? $actual['estado']); 

    if (empty($tipoResiduo) || empty($ubicacion)) {
        responder(400, null, 'Tipo de residuo y ubicaciónson son obligatorios');
    }

    if (!is_numeric($capacidad) || (float) $capacidad <= 0) {
        responder(400, null, 'La capacidad debe ser un número mayor que cero');
    }

    if (!in_array($estado, $estadosValidos, true)) {
        responder(400, null, 'El estado debe ser funcional, roto o desbordado');
    }

    $stmt = $pdo->prepare("
    UPDATE contenedor
    SET tipo_residuo = ?,
    capacidad = ?,
    ubicacion = ?, 
    estado = ?
    WHERE id_contenedor = ?");

    $stmt->execute([
        $tipoResiduo, 
        (float) $capacidad, 
        $ubicacion, 
        $estado,
        $id]);

    $contenedorActualizado = [
        'id_contenedor' => $id,
        'tipo_residuo' => $tipoResiduo,
        'capacidad' => (float) $capacidad,
        'ubicacion' => $ubicacion,
        'estado' => $estado];

    responder(200, $contenedorActualizado, 'Contenedor actualizado correctamente');

} catch (PDOException $e) {
    responder(500, null, 'Error al modificar el contenedor');
}
?>