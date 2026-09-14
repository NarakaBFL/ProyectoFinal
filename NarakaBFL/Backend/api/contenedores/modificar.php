<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    responder(405, null, 'Método no permitido');
}

$body = obtenerBody();
$id = $body['id_contenedor'] ?? null;

if (empty($id) || !is_numeric($id)) {
    responder(400, null, 'Falta el id_contenedor a modificar');
}

$estadosValidos = [
    'funcional',
    'roto',
    'desbordado'
];

try {
    // Verificar que exista y esté activo
    $stmt = $pdo->prepare("SELECT * FROM contenedor WHERE id_contenedor = :id AND activo = 1");
    $stmt->execute([':id' => $id]);
    $actual = $stmt->fetch();

    if (!$actual) {
        responder(404, null, 'Contenedor no encontrado');
    }

    // RF-06.4: modificación de datos. Se mantiene el valor actual si no viene en el body.
    $tipoResiduo = trim($body['tipo_residuo'] ?? $actual['tipo_residuo']);
    $capacidad   = $body['capacidad'] ?? $actual['capacidad'];
    $ubicacion   = trim($body['ubicacion'] ?? $actual['ubicacion']);
    $direccion   = trim($body['direccion'] ?? $actual['direccion']);
    $estado      = trim($body['estado'] ?? $actual['estado']); // RF-07.2

    if (empty($tipoResiduo) || empty($ubicacion) || empty($direccion)) {
        responder(400, null, 'Tipo de residuo, ubicación y dirección son obligatorios');
    }

    if (!is_numeric($capacidad) || (float) $capacidad <= 0) {
        responder(400, null, 'La capacidad debe ser un número mayor que cero');
    }

    if (!in_array($estado, $estadosValidos, true)) {
        responder(400, null, 'El estado debe ser funcional, roto o desbordado');
    }

    $sql = "UPDATE contenedor
            SET tipo_residuo = :tipo_residuo,
                capacidad = :capacidad,
                ubicacion = :ubicacion,
                direccion = :direccion,
                estado = :estado
            WHERE id_contenedor = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tipo_residuo' => $tipoResiduo,
        ':capacidad'    => (float) $capacidad,
        ':ubicacion'    => $ubicacion,
        ':direccion'    => $direccion,
        ':estado'       => $estado,
        ':id'           => $id,
    ]);

    $contenedorActualizado = [
        'id_contenedor' => (int) $id,
        'tipo_residuo'  => $tipoResiduo,
        'capacidad'     => (float) $capacidad,
        'ubicacion'     => $ubicacion,
        'direccion'     => $direccion,
        'estado'        => $estado,
    ];

    responder(200, $contenedorActualizado, 'Contenedor actualizado correctamente');
} catch (PDOException $e) {
    responder(500, null, 'Error al modificar el contenedor');
}
?>
