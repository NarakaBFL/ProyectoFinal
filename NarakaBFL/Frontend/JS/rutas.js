const tabla = document.getElementById('tablaRutas');

async function cargarRutas() {

    try {

        const res = await fetch(
            '../../Backend/api/rutas/listar.php', 
            {
                credentials: 'include'
            }
        );

        const data = await res.json();

        if (!res.ok) {
            alert(data.mensaje || 'Error al cargar las rutas');
            return;
        }

        tabla.innerHTML = '';

        data.data.forEach(ruta => {

            tabla.innerHTML += `
            <tr>
            <td>${ruta.id_ruta}</td>
            <td>${ruta.num_ruta}</td>
            <td>${ruta.zona}</td>
            <td>${ruta.recorrido}</td>
            <td>${ruta.camion}</td>
            <td>${ruta.cuadrilla}</td>

            <td>

                <a
                    href="formRuta.php?id=${ruta.id_ruta}"
                    class="btn-editar">
                    Editar
                </a>

                <button
                    class="btn-eliminar"
                    onclick="eliminarRuta(${ruta.id_ruta})">
                    Eliminar
                    </button>
            
            </td>

            </tr>

            `;
        });

    }catch (error) {

        console.error('Error al cargar rutas:', error);

        alert('No se pudieron cargar las rutas');
    }
}

async function eliminarRuta(id) {

    if(!confirm('¿Desea eliminar esta ruta?')) {
        return;
    }

    try {

        const res = await fetch(
            '../../Backend/api/rutas/eliminar.php',
            {
                method: 'DELETE',

                headers: {
                    'Content-Type': 'application/json'
                },

                credentials: 'include',

                body: JSON.stringify({
                    id_ruta: id
                })
            }
        );

        const data = await res.json();

        alert(data.mensaje);

        if (res.ok) {
            cargarRutas();
        }

    }catch (error) {

        console.error('Error al eliminar ruta:', error);

        alert('No se pudo eliminar la ruta');

    }
}

cargarRutas();