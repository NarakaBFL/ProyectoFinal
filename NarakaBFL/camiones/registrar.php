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

if ((int) $_SESSION['usuario_actual']['id_rol'] !== 2) {
    responder(403, null, 'No tienes permiso para realizar esta acción');
}

$body = obtenerBody();

$matricula = strtoupper(trim($body['matricula'] ?? ''));

$estado = trim($body['estado'] ?? 'disponible');

$capacidad = $body['capacidad'] ?? 0;

$estadosValidos = [
    'disponible',
    'en_servicio',
    'mantenimiento',
    'fuera_de_servicio'
];

if (empty($matricula)) {
    responder(400, null, 'La matrícula es obligatoria');
}

if (!is_numeric($capacidad) || (float) $capacidad <= 0) {
    responder(400, null, 'La capacidad debe ser un número mayor que cero');
}

if (!in_array($estado, $estadosValidos, true)) {
    responder(400, null, 'El estado del camión no es válido');
}

// Comprobar que la matrícula no esté repetida.
$stmt = $pdo->prepare("
SELECT id_camion
FROM camion 
WHERE matricula = ?");

$stmt->execute([$matricula]);

if($stmt->fetch()) {
    responder(409, null, 'La matrícula ya está registrada');
}

// El administrador que inició sesión queda como usuario que asigna
$idUsuarioAsigna = $_SESSION['usuario_actual']['id'];

$stmt = $pdo->prepare("
INSERT INTO camion (matricula, estado, capacidad, id_usuario_asigna)
VALUES (?, ?, ?, ?)");

$stmt->execute([
    $matricula,
    $estado,
    $capacidad,
    $idUsuarioAsigna
]);

$nuevoCamion = [
    'id_camion' => $pdo->lastInsertId(),
    'matricula' => $matricula,
    'estado' => $estado,
    'capacidad' => (float) $capacidad,
    'id_usuario_asigna' => $idUsuarioAsigna
];

responder(201, $nuevoCamion, 'Camión registrado correctamente');