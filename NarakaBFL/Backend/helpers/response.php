<?php
function responder(int $status, mixed $data = null, string $mensaje = ''): void {

    http_response_code($status);

    echo json_encode(
        [
        'success' => $status >= 200 && $status < 300,
        'mensaje' => $mensaje,
        'data' => $data
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit();

}

function obtenerBody(): array {
    if (!empty($_POST)){
    return $_POST;
}

$contenido = file_get_contents('php://input');

if($contenido === false || trim($contenido) === '') {
    return [];
}

$body = json_decode($contenido, true);

if(!is_array($body)) {
    responder(400, null, 'El cuerpo de la solicitud debe ser un JSON válido');
}

return $body;

}