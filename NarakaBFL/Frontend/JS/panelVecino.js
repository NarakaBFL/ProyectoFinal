const tablaIncidencias = document.getElementById('tablaIncidencias');

async function cargarIncidencias() {
    try {

        const respuesta = await fetch(
            '../../Backend/api/incidencias/misIncidencias.php',
            {
                method: 'GET',
                credentials: 'include'
            }
        );

        const datos = await respuesta.json();

        if (!respuesta.ok) {
            console.error(datos.mensaje);
            return;
        }

        tablaIncidencias.innerHTML = '';

        datos.data.forEach(incidencia => {

            const fila = document.createElement('tr');

            fila.innerHTML = `
        <td>${incidencia.id_incidencia}</td>
        <td>${incidencia.calle}</td>
        <td>${incidencia.tipo}</td>
        <td>${incidencia.estado}</td>
        <td>-</td>
        <td>
            <button>Ver</button>
        </td>
        `;

            tablaIncidencias.appendChild(fila);

        });
    } catch (error) {
        console.error('Error al cargar las incidencias');
    }
}

cargarIncidencias();