<?php
function responder($status, $data = null, $mensaje = '') {
    http_response_code($status);
    echo json_encode([
        'success' => $status >= 200 && $status < 300,
        'mensaje' => $mensaje,
        'data' => $data
    ]);
    exit();
}

function obtenerBody() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}