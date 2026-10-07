document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('formIncidenciaAdmin');
    const selectContenedor = document.getElementById('id_contenedor');

    const API_CONTENEDORES = '../../Backend/api/contenedores/listar.php';
    const API_INCIDENCIAS = '../../Backend/api/incidencias/registrar.php';

    //Cargar contenedores reales desde la base de datos
    async function cargarContenedores() {

        try {

            const respuesta = await fetch (API_CONTENEDORES, {
                method: 'GET',
            credentials: 'include'
            });

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                alert(datos.mensaje || 'Error al cargar los contenedores');
                return;
            }

            selectContenedor.innerHTML =
            '<option value="">Seleccione un contenedor</option>';

            datos.data.forEach(contenedor => {

                const option = document.createElement('option');

                option.value = contenedor.id_contenedor;

                option.textContent = 
                `${contenedor.ubicacion} - ID ${contenedor.id_contenedor}`;

                selectContenedor.appendChild(option);
            });

        } catch (error) {

            console.error('Error:', error);

            alert('No se pudieron cargar los contenedores');
        }
    }

    //Registrar incidencia
    form.addEventListener('submit', async (event) => {

        event.preventDefault();

        const tipo = document.getElementById('tipo').value;
        const descripcion = document.getElementById('descripcion').value.trim();
        const idContenedor = document.getElementById('id_contenedor').value;

        const incidencia = {
            tipo: tipo,
            descripcion: descripcion,
            id_contenedor: Number(idContenedor)
        };

        try {

            const respuesta = await fetch (API_INCIDENCIAS, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(incidencia)
            });

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                alert(datos.mensaje || 'Error al registrar la incidencia');
                return;
            }

            alert(datos.mensaje);

            window.location.href = 'incidencias.html';

        }catch (error){

            console.error('Error:', error);

            alert('No se pudo registrar la incidencia');
        }
    });

    cargarContenedores();

});