const navbar = document.getElementById('navbar');

navbar.innerHTML = `
<nav class="navbar">
        <h1>SiGeRU</h1>

        <div class="nav-links">
            <ul>
                <li><a href="panelAdmin.html">Panel</a></li>
                <li><a href="usuarios.html">Usuarios</a></li>
                <li><a href="contenedores.html">Contenedores</a></li>
                <li><a href="camiones.html">Camiones</a></li>
                <li><a href="incidencias.html">Incidencias</a></li>
                <li><a href="centrosAcopio.html">Centros de acopio</a></li>
                <li><a href="maquinaria.html">Maquinaria</a></li>
                <li><a href="login.html">Cerrar sesión</a></li>
            </ul>
        </div>
    </nav>
`;