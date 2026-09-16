<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

if (!isset($_SESSION['usuario_actual'])) {
    responder(401, null, 'No hay una sesión activa');
}

if ((int) $_SESSION['usuario_actual']['id_rol'] !== 1) {
    responder(403, null, 'No tienes permiso para realizar esta acción');
}

$datos = obtenerBody();

$tipo = trim($datos['tipo'] ?? '');
$descripcion = trim($datos['descripcion'] ?? '');
$idContenedor = (int) ($datos['id_contenedor'] ?? 0);

$idUsuario = (int) $_SESSION['usuario_actual']['id'];

$tiposValidos = [
    'rotura',
    'desborde',
    'falta_recoleccion',
    'otro'
];

if (empty($tipo)) {
    responder(400, null, 'El tipo de incidencia es obligatorio');
}

if (!in_array($tipo, $tiposValidos, true)) {
    responder(400, null, 'El tipo de incidencia no es válido');
}

if (empty($descripcion)) {
    responder(400, null, 'La descripción es obligatoria');
}

if ($idContenedor <= 0) {
    responder(400, null, 'Debe seleccionar una calle');
}

// Verificar que el contenedor exista
$stmt = $pdo->prepare("
SELECT id_contenedor
FROM contenedor
WHERE id_contenedor = ?");

$stmt->execute([$idContenedor]);

if (!$stmt->fetch()) {
    responder(404, null, 'El contenedor seleccionado no existe');
}

// Registrar incidencia
$stmt = $pdo->prepare("
INSERT INTO incidencia (tipo, descripcion, estado, id_contenedor, id_usuario)
VALUES (?, ?, 'abierta', ?, ?)");

$stmt->execute([
    $tipo,
    $descripcion,
    $idContenedor,
    $idUsuario
]);

responder(201, ['id_incidencia' => $pdo->lastInsertId()], 'Incidencia registrada correctamente');
?>