const tabla = document.getElementById('tablaCamiones');

async function cargarCamiones() {
    try {
        const res = await fetch('../../Backend/api/camiones/listar.php', {
            credentials: 'include'
        });

        const data = await res.json();

        tabla.innerHTML = '';

        data.data.forEach(camion => {
            tabla.innerHTML += `
    <tr>
        <td>${camion.id_camion}</td>
        <td>${camion.matricula}</td>
        <td>${camion.capacidad}</td>
        <td>${estadosTexto[camion.estado] ?? camion.estado}</td>
        <td>
        <a href="formCamiones.html?id=${camion.id_camion}"
        class="btn-editar">
        Editar
        </a>

        <button
        class="btn-eliminar"
        onclick="eliminarCamion(${camion.id_camion})">
        Eliminar
        </button>
        </td>
    </tr>
    `;
        });
    } catch (error) {
        console.error('Error al cargar camiones:', error);
    }
}

async function eliminarCamion(id) {
    if (!confirm('¿Desea eliminar este camión?')) {
        return;
    }

    try {
        const res = await fetch('../../Backend/api/camiones/eliminar.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                id_camion: id
            })
        });

        const data = await res.json();

        alert(data.mensaje);

        if (res.ok) {
            cargarCamiones();
        }
    } catch (error) {
        console.error('Error al eliminar camión:', error);
    }
}

const estadosTexto = {
    disponible: 'Disponible', 
    en_servicio: 'En servicio',
    mantenimiento: 'En mantenimiento',
    fuera_de_servicio: 'Fuera de servicio'};

cargarCamiones();
