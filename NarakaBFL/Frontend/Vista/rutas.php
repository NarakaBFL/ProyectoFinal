<?php
require_once __DIR__ . '/includes/protegerPagina.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rutas - Naraka BFL</title>
    <link rel="stylesheet" href="../CSS/estilo.css">
</head>

<body data-roles="2">

    <?php require_once __DIR__ . '/includes/header.php'; ?>

    <main class="gestion-main">

        <h2>Gestión de rutas</h2>

        <p>
            Listado de rutas registradas en el sistema.
        </p>

        <div class="acciones">
            <a href="formRuta.php" class="btn-nuevo">+ Nueva Ruta</a>
        </div>

        <div class="tabla-form">

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>N° Ruta</th>
                        <th>Zona</th>
                        <th>Recorrido</th>
                        <th>Camión</th>
                        <th>Cuadrilla</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaRutas">
                    <!-- Las rutas vendrán desde MySQL -->
                </tbody>

            </table>

        </div>

    </main>

    <footer>

        <p>© Naraka BFL - SiGeRU</p>

        <div class="footer-links">
            <a href="panelAdmin.php" style="text-decoration: none;">Panel</a>
            <a href="inicio.html" style="text-decoration: none;">Inicio</a>
        </div>

    </footer>

    <script src="../JS/auth.js" defer></script>
    <script src="../JS/rutas.js" defer></script>
    
</body>

</html>
