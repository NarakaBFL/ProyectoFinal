<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();

$tipoResiduo = trim($body['tipo_residuo'] ?? '');
$capacidad   = $body['capacidad'] ?? 0;
$ubicacion   = trim($body['ubicacion'] ?? '');
$direccion   = trim($body['direccion'] ?? '');
$estado      = trim($body['estado'] ?? 'funcional');

$estadosValidos = [
    'funcional',
    'roto',
    'desbordado'
];

if (empty($tipoResiduo) || empty($ubicacion) || empty($direccion)) {
    responder(400, null, 'Tipo de residuo, ubicación y dirección son obligatorios');
}

if (!is_numeric($capacidad) || (float) $capacidad <= 0) {
    responder(400, null, 'La capacidad debe ser un número mayor que cero');
}

if (!in_array($estado, $estadosValidos, true)) {
    responder(400, null, 'El estado debe ser funcional, roto o desbordado');
}

try {
    $sql = "INSERT INTO contenedor (tipo_residuo, capacidad, ubicacion, direccion, estado, activo)
            VALUES (:tipo_residuo, :capacidad, :ubicacion, :direccion, :estado, 1)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tipo_residuo' => $tipoResiduo,
        ':capacidad'    => (float) $capacidad,
        ':ubicacion'    => $ubicacion,
        ':direccion'    => $direccion,
        ':estado'       => $estado,
    ]);

    $nuevoContenedor = [
        'id_contenedor' => (int) $pdo->lastInsertId(),
        'tipo_residuo'  => $tipoResiduo,
        'capacidad'     => (float) $capacidad,
        'ubicacion'     => $ubicacion,
        'direccion'     => $direccion,
        'estado'        => $estado,
    ];

    responder(201, $nuevoContenedor, 'Contenedor registrado correctamente');
} catch (PDOException $e) {
    responder(500, null, 'Error al registrar el contenedor');
}
?>
