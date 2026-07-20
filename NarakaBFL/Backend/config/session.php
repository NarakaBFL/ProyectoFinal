<?php
session_start();
header('Content-Type: application/json');
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
            'nombre' => 'Admin',
            'email' => 'admin@sigeru.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'rol' => 'Administrador'
        ]
    ];
}
if (!isset($_SESSION['contenedores'])) {
    $_SESSION['contenedores'] = [];
}
if (!isset($_SESSION['camiones'])) {
    $_SESSION['camiones'] = [];
}