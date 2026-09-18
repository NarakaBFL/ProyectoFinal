const form = document.getElementById('incidenciaForm');
const mensaje = document.getElementById('mensaje');
const selectContenedor = document.getElementById('id_contenedor');

async function cargarCalles() {
    try {
        const respuesta = await fetch(
            '../../Backend/api/contenedores/listarCalles.php',
            {
                method: 'GET',
                credentials: 'include'
            }
        );

        const datos = await respuesta.json();

        if (!respuesta.ok) {
            mensaje.textContent = datos.mensaje || 'Error al cargar las calles';
            return;
        }

        datos.data.forEach(contenedor => {
            const opcion = document.createElement('option');

            opcion.value = contenedor.id_contenedor;
            opcion.textContent = contenedor.ubicacion;

            selectContenedor.appendChild(opcion);
        });

    } catch (error){
        mensaje.textContent = 'Error de conexión al cargar las calles';
    }
}

form.addEventListener('submit', async function (e) {
    e.preventDefault();
    
    const tipo = document.getElementById('tipo').value;
    const descripcion = document.getElementById('descripcion').value.trim();
    const idContenedor = Number(selectContenedor.value);

    const datos = {
        tipo: tipo,
        descripcion: descripcion,
        id_contenedor: idContenedor
    };

    try {
        const respuesta = await fetch(
            '../../Backend/api/incidencias/registrar.php',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(datos)
            }
        );
        
        const resultado = await respuesta.json();

        mensaje.textContent = resultado.mensaje;

        if (respuesta.ok) {
            setTimeout(() => {
                window.location.href = 'panelVecino.html';
            }, 1000);
        }
    } catch (error) {
        mensaje.textContent = 'Error de conexión al registrar la incidencia';
    }
});

cargarCalles();