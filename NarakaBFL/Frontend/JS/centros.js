const API_BASE = '../../Backend/api/centros';

const tablaCentros = document.getElementById('tablaCentros');
const formCentro = document.getElementById('formCentro');

if (tablaCentros) {
    cargarCentros();
}

if (formCentro) {
    inicializarFormulario();
}

async function cargarCentros() {

    try {

        const respuesta = await fetch(
            `${API_BASE}/listar.php`,
            {
                method: 'GET',
                credentials: 'include'
            }
        );

        const datos = await respuesta.json();

        if (!respuesta.ok) {
            alert(datos.mensaje || 'Error al cargar los centros de acopio');
            return;
        }

        mostrarCentros(datos.data);

    } catch (error) {

        console.error(error);
        alert('Error de conexión con el servidor');
    }
}

function mostrarCentros(centros) {

    const mensajeVacio = document.getElementById('mensajeVacio');

    tablaCentros.innerHTML = '';

    if (!centros || centros.length === 0) {

        mensajeVacio.style.display = 'block';
        return;
    }

    mensajeVacio.style.display = 'none';

    centros.forEach(centro => {

        const capacidad = Number(centro.capacidad_total);
        const ocupacion = Number(centro.ocupacion_actual);

        const porcentaje = capacidad > 0
            ? ((ocupacion / capacidad) * 100).toFixed(2) 
            : 0;

        const alerta = porcentaje >= 80
            ? ' ⚠️' 
            : '';

        const fila = document.createElement('tr');

        fila.innerHTML = `
        <td>${centro.id_centro}</td>
        <td>${centro.nombre}</td>
        <td>${centro.ubicacion}</td>
        <td>${centro.tipo_residuo}</td>
        <td>${centro.capacidad_total}</td>
        <td>${centro.ocupacion_actual}</td>
        <td>${porcentaje}%${alerta}</td>

        <td>
        <a href="formCentro.html?id=${centro.id_centro}"
           class="btn-editar"> Editar </a>

           <button
           class="btn-eliminar"
           onclick="eliminarCentro(${centro.id_centro})">
           Eliminar
           </button>
        </td>
        `;
        tablaCentros.appendChild(fila);
    });
}

async function eliminarCentro(id) {

    const confirmar = confirm(
        '¿Está seguro que desea eliminar este centro de acopio?'
    );

    if (!confirmar) {
        return;
    }

    try {

        const respuesta = await fetch(
            `${API_BASE}/eliminar.php`,
            {
                method: 'DELETE',

                headers: {
                    'Content-Type': 'application/json'
                },

                credentials: 'include',

                body: JSON.stringify({
                    id_centro: id
                })
            }
        );

        const datos = await respuesta.json();

        if (!respuesta.ok) {
            alert(
                datos.mensaje ||
                'Error al eliminar el centro de acopio'
            );
            return;
        }

        alert(datos.mensaje);

        cargarCentros();

    } catch (error) {

        console.error(error);

        alert(
            'Error de conexión con el servidor'
        );
    }
}


function inicializarFormulario() {

    const parametros =
        new URLSearchParams(window.location.search);

    const id = parametros.get('id');

    if (id) {

        document.getElementById('tituloForm').textContent =
            'Editar Centro de Acopio';

        document.getElementById('grupoOcupacion').style.display =
            'block';

        document.getElementById('id_centro').value = id;

        cargarDatosCentro(id);
    }

    formCentro.addEventListener(
        'submit',
        guardarCentro
    );
}


async function cargarDatosCentro(id) {

    try {

        const respuesta = await fetch(
            `${API_BASE}/listar.php`,
            {
                method: 'GET',
                credentials: 'include'
            }
        );

        const datos = await respuesta.json();

        if (!respuesta.ok) {
            mostrarError(datos.mensaje);
            return;
        }

        const centro = datos.data.find(
            centro => centro.id_centro == id
        );

        if (!centro) {
            mostrarError(
                'Centro de acopio no encontrado'
            );
            return;
        }

        document.getElementById('nombre').value =
            centro.nombre;

        document.getElementById('ubicacion').value =
            centro.ubicacion;

        document.getElementById('tipo_residuo').value =
            centro.tipo_residuo;

        document.getElementById('capacidad_total').value =
            centro.capacidad_total;

        document.getElementById('ocupacion_actual').value =
            centro.ocupacion_actual;

    } catch (error) {

        console.error(error);

        mostrarError(
            'Error de conexión con el servidor'
        );
    }
}


async function guardarCentro(evento) {

    evento.preventDefault();

    ocultarError();

    const idCentro =
        document.getElementById('id_centro').value;

    const esEdicion = idCentro !== '';

    const datos = {

        nombre:
            document.getElementById('nombre').value.trim(),

        ubicacion:
            document.getElementById('ubicacion').value.trim(),

        tipo_residuo:
            document.getElementById('tipo_residuo').value,

        capacidad_total:
            Number(
                document.getElementById('capacidad_total').value
            )
    };

    if (esEdicion) {

        datos.id_centro = Number(idCentro);

        datos.ocupacion_actual =
            Number(
                document.getElementById(
                    'ocupacion_actual'
                ).value || 0
            );
    }

    const url = esEdicion
        ? `${API_BASE}/editar.php`
        : `${API_BASE}/registrar.php`;

    const metodo = esEdicion
        ? 'PUT'
        : 'POST';

    try {

        const respuesta = await fetch(
            url,
            {
                method: metodo,

                headers: {
                    'Content-Type': 'application/json'
                },

                credentials: 'include',

                body: JSON.stringify(datos)
            }
        );

        const resultado =
            await respuesta.json();

        if (!respuesta.ok) {

            mostrarError(
                resultado.mensaje ||
                'Error al guardar el centro'
            );

            return;
        }

        if (
            resultado.data &&
            resultado.data.alerta_capacidad
        ) {
            alert(
                'Atención: el centro superó el 80% de su capacidad'
            );
        }

        window.location.href =
            'centros.html';

    } catch (error) {

        console.error(error);

        mostrarError(
            'Error de conexión con el servidor'
        );
    }
}


function mostrarError(mensaje) {

    const elemento =
        document.getElementById('mensajeError');

    elemento.textContent = mensaje;
}


function ocultarError() {

    const elemento =
        document.getElementById('mensajeError');

    elemento.textContent = '';
}