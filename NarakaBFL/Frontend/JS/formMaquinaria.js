const form = document.getElementById('maquinariaForm');
const mensaje = document.getElementById('mensaje');
const tituloFormulario = document.getElementById('tituloFormulario');

const params = new URLSearchParams(window.location.search);
const idMaquinaria = params.get('id');

// Si hay un ID en la URL, estamos editando
if (idMaquinaria) {
    tituloFormulario.textContent = 'Editar maquinaria';
    cargarMaquinaria();
}

// Cargar los datos de la maquinaria seleccionada
async function cargarMaquinaria() {
    try {
        const res = await fetch(
            '../../Backend/api/maquinaria/listar.php',
            {
                credentials: 'include'
            }
        );

        const data = await res.json();

        const maquina = data.data.find(
            m => Number(m.id_maquinaria) === Number(idMaquinaria)
        );

        if (!maquina) {
            mensaje.textContent = 'No se encontró la maquinaria';
            return;
        }
        
        document.getElementById('tipo').value = maquina.tipo;

        document.getElementById('estado').value = maquina.estado;

        document.getElementById('fecha_ultimo_mantenimiento').value = 
        maquina.fecha_ultimo_mantenimiento ?? '';

        document.getElementById('id_centro').value = maquina.id_centro;

    } catch (error) {

        console.error('Error al cargar maquinaria:', error);

        mensaje.textContent = 'Error al cargar los datos de la maquinaria';
    }
}

// Registrar o editar
form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const tipo = document.getElementById('tipo').value.trim();

    const estado = document.getElementById('estado').value;

    const fechaUltimoMantenimiento = 
    document.getElementById('fecha_ultimo_mantenimiento').value;

    const idCentro = document.getElementById('id_centro').value;

    const datos = {
        tipo: tipo,
        estado: estado,
        fecha_ultimo_mantenimiento: fechaUltimoMantenimiento,
        id_centro: Number(idCentro)
    };

    // Si estamos editando, agregamos el ID
    if (idMaquinaria) {
        datos.id_maquinaria = Number(idMaquinaria);
    }

    const url = idMaquinaria
        ? '../../Backend/api/maquinaria/editar.php'
        : '../../Backend/api/maquinaria/registrar.php';

    const metodo = idMaquinaria
    ? 'PUT'
    : 'POST';

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

            window.location.href = 'maquinaria.html';

        }
    } catch (error) {
        
        console.error('Error al guardar maquinaria:', error);

        mensaje.textContent = 'Ocurrió un error al guardar la maquinaria';
    }

});