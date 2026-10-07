document.addEventListener('DOMContentLoaded', () => {

    const tablaIncidencias = document.getElementById('tablaIncidencias');

    const API_INCIDENCIAS = '../../Backend/api/incidencias/listar.php';

    const API_CAMBIAR_ESTADO = '../../Backend/api/incidencias/cambiarEstado.php';

    async function cargarIncidencias() {

        try {

            const respuesta = await fetch(API_INCIDENCIAS, {
                method: 'GET',
                credentials: 'include'
            });

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                alert (datos.mensaje || 'Error al cargar las incidencias');
                return;
            }

            tablaIncidencias.innerHTML = '';

            datos.data.forEach(incidencia => {

                const fila = document.createElement('tr');

                fila.innerHTML = `
                <td>${incidencia.id_incidencia}</td>
                <td>${formatearFecha(incidencia.fecha)}</td>
                <td>${incidencia.tipo}</td>
                <td>${incidencia.descripcion}</td>
                <td>${incidencia.contenedor}</td>
                <td>${incidencia.usuario}</td>
                <td>${incidencia.estado}</td>
                <td>
                <select class="estado-incidencia">

                    <option value="abierta"
                    ${incidencia.estado === 'abierta' ? 'selected' : ''}>Abierta</option>

                    <option value="en_curso"
                    ${incidencia.estado === 'en_curso' ? 'selected' : ''}>En curso</option>

                    <option value="resuelta"
                    ${incidencia.estado === 'resuelta' ? 'selected' : ''}>Resuelta</option>

                </select>

                <button type="button" 
                class="btn-editar btn-estado"
                data-id="${incidencia.id_incidencia}">Guardar</button>
                </td>
                `;    
                
                tablaIncidencias.appendChild(fila);
            });

        }catch (error) {

            console.error('Error:', error);

            alert('No se pudieron cargar las incidencias');
        }   
    }

    //Cambiar estado de incidencia
    tablaIncidencias.addEventListener('click', async (event) => {

        if (!event.target.classList.contains('btn-estado')) {
            return;
        }

        const boton = event.target;

        const idIncidencia = Number(boton.dataset.id);

        const fila = boton.closest('tr');

        const selectEstado =
            fila.querySelector('.estado-incidencia');

        const estado = selectEstado.value;


        try {

            const respuesta = await fetch(API_CAMBIAR_ESTADO, {

                method: 'PUT',

                headers: {
                    'Content-Type': 'application/json'
                },

                credentials: 'include',

                body: JSON.stringify({
                    id_incidencia: idIncidencia,
                    estado: estado
                })
            });

            const datos = await respuesta.json();

            if (!respuesta.ok) {

                alert(
                    datos.mensaje ||
                    'Error al cambiar el estado');

                return;
            }

            alert(datos.mensaje);

            cargarIncidencias();

        } catch (error) {

            console.error('Error:', error);

            alert('No se pudo cambiar el estado de la incidencia');
        }
    });

    function formatearFecha(fecha) {

        if (!fecha) {
            return '-';
        }

        const partes = fecha.split(' ');

        const fechaParte = partes[0];
        const horaParte = partes[1] || '';

        const [anio, mes, dia] = fechaParte.split('-');

        return `${dia}/${mes}/${anio} ${horaParte}`;
    }

    cargarIncidencias();
    
});