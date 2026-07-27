<?php

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();

$matricula = strtoupper(
    trim($body['matricula'] ?? '')
);

$estado = trim($body['estado'] ?? 'disponible');

$capacidad = $body['capacidad'] ?? 0;

$idUsuarioAsigna = $body['id_usuario_asigna'] ?? null;

$estadosValidos = [
    'disponible',
    'en_servicio',
    'mantenimiento',
    'fuera_de_servicio'
];

if (empty($matricula) || $idUsuarioAsigna === null) {
    responder(400, null, 'Matrícula y usuario que asigna son obligatorios');
}

if (!is_numeric($capacidad) || (float) $capacidad <= 0) {
    responder(400, null, 'La capacidad debe ser un número mayor que cero');
}

if (!in_array($estado, $estadosValidos, true)) {
    responder(400, null, 'El estado del camión no es válido');
}

if (filter_var($idUsuarioAsigna, FILTER_VALIDATE_INT) === false) {
    responder(400, null, 'El identificador del usuario no es válido');
}

$idUsuarioAsigna = (int) $idUsuarioAsigna;

// Comprobar que el usuario exista en la sesión.
$usuarioEncontrado = false;

foreach ($_SESSION['usuarios'] as $usuario) {
    if (isset($usuario['id']) && (int) $usuario['id'] === $idUsuarioAsigna) {
        $usuarioEncontrado = true;
        break;
    }
}

if (!$usuarioEncontrado) {
    responder(400, null, 'El usuario que asigna no existe');
}

// Comprobar que la matrícula no esté repetida.
foreach ($_SESSION['camiones'] as $camion) {
    if (isset($camion['matricula']) && strtoupper($camion['matricula']) === $matricula) {
        responder(409, null, 'La matrícula ya está registrada');
    }
}

// Generar el siguiente identificador.
$nuevoId = 1;

if (!empty($_SESSION['camiones'])) {
    $ids = array_column(
        $_SESSION['camiones'],
        'id_camion'
    );

    $nuevoId = max($ids) + 1;
}

$nuevoCamion = [
    'id_camion' => $nuevoId,
    'matricula' => $matricula,
    'estado' => $estado,
    'capacidad' => (float) $capacidad,
    'id_usuario_asigna' => $idUsuarioAsigna
];

$_SESSION['camiones'][] = $nuevoCamion;

responder(201, $nuevoCamion, 'Camión registrado correctamente');