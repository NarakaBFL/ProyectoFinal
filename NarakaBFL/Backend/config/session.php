<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inicializar "tablas" simuladas en sesión si no existen
if (!isset($_SESSION['usuarios'])) {
    $_SESSION['usuarios'] = [
        [
            'id' => 1,
            'nombre' => 'Usuario',
            'apellido' => 'Vecino',
            'email' => 'vecino@sigeru.com',
            'contrasena' => password_hash('vecino123', PASSWORD_DEFAULT),

            //Rol 1 es vecino
            'id_rol' => 1,

            //Se conserva también el nombre para mostrarlo fácilmente
            'rol' => 'Vecino',

            'activo' => true
        ]
    ];
}

//Tabla simulada de contenedores
if (!isset($_SESSION['contenedores'])) {
    $_SESSION['contenedores'] = [];
}

//Tabla simulada de camiones
if (!isset($_SESSION['camiones'])) {
    $_SESSION['camiones'] = [];
}

?>