const form = document.getElementById('camionForm');
const mensaje = document.getElementById('mensaje');

const params = new URLSearchParams(window.location.search);
const idCamion = params.get('id');

// Si viene un ID, estamos editando
if (idCamion) {
    cargarCamion();
}

async function cargarCamion() {
    try {
        const res = await fetch('../../Backend/api/camiones/listar.php', {
            credentials: 'include'
        });

        const data = await res.json();

        const camion = data.data.find(
            c => Number(c.id_camion) === Number(idCamion)
        );

        if (!camion) {
            mensaje.textContent = 'Camión no encontrado';
            return;
        }

        document.getElementById('matricula').value = camion.matricula;
        document.getElementById('capacidad').value = camion.capacidad;
        document.getElementById('estado').value = camion.estado;

    }catch (error) {
        mensaje.textContent = 'Error al cargar el camión';
    }
}

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const matricula = document.getElementById('matricula').value.trim();
    const capacidad = document.getElementById('capacidad').value;
    const estado = document.getElementById('estado').value;

    const datos = {
        matricula, 
        capacidad,
        estado
    };

    // Si estamos editando, agregamos el ID
    if (idCamion) {
        datos.id_camion = idCamion;
    }

    const url = idCamion
    ? '../../Backend/api/camiones/editar.php'
    : '../../Backend/api/camiones/registrar.php';

    const metodo = idCamion ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, {
            method: metodo, 
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify(datos)
        });

        const data = await res.json();

        mensaje.textContent = data.mensaje;

        if (res.ok) {
            window.location.href = 'camiones.html';
        }
    }catch (error) {
        mensaje.textContent = 'Error al conectar con el servidor';
    }
});