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

$numRuta = (int) ($body['num_ruta'] ?? 0);
$zona = trim($body['zona'] ?? '');
$recorrido = trim($body['recorrido'] ?? '');
$idCamion = (int) ($body['id_camion'] ?? 0);
$idCuadrilla = (int) ($body['id_usuario_cuadrilla'] ?? 0);

//Validar campos
if ($numRuta <= 0 || $zona === '' || $recorrido === '' || $idCamion <= 0 || $idCuadrilla <= 0) {
    responder(400, null, 'Todos los campos son obligatorios');
}

if (strlen($zona) > 100) {
    responder(400, null, 'La zona no puede superar los 100 caracteres');
}

if (strlen($recorrido) > 255) {
    responder(400, null, 'El recorrido no puede superar los 255 caracteres');
}

try {

    //Verificar que no exista el número de ruta
    $stmt = $pdo->prepare("
    SELECT id_ruta
    FROM ruta
    WHERE num_ruta = ?");

    $stmt->execute([$numRuta]);

    if ($stmt->fetch()) {
        responder(409, null, 'Ya existe una ruta con ese número');
    }

    //Verificar que exista el camión
    $stmt = $pdo->prepare("
    SELECT id_camion
    FROM camion
    WHERE id_camion = ?");

    $stmt->execute([$idCamion]);

    if (!$stmt->fetch()) {
        responder(404, null, 'El camión seleccionado no existe');
    }

    //Verificar que el usuario exista y sea Cuadrilla
    $stmt = $pdo->prepare("
    SELECT id_usuario
    FROM usuario
    WHERE id_usuario = ?
    AND id_rol = 4
    AND activo = 1");

    $stmt->execute([$idCuadrilla]);

    if (!$stmt->fetch()) {
        responder(400, null, 'El usuario seleccionado no corresponde a una cuadrilla activa');
    }

    //Registrar ruta
    $stmt = $pdo->prepare("
    INSERT INTO ruta (
    num_ruta,
    zona,
    recorrido,
    id_camion,
    id_usuario_cuadrilla)
    VALUES (?, ?, ?, ?, ?)");

    $stmt->execute([
        $numRuta,
        $zona,
        $recorrido,
        $idCamion,
        $idCuadrilla]);

        responder(201, [
            'id_ruta' => (int) $pdo->lastInsertId()
            ],
            'Ruta registrada correctamente');


}catch (PDOException $e) {
    responder(500, null, 'Error al registrar la ruta');
}
?>