document.addEventListener('DOMContentLoaded', () => {

    const API_BASE = '../../Backend/api/contenedores';

    const tablaContenedores = document.getElementById('tablaContenedores');
    const formContenedor = document.getElementById('formContenedor');

    const parametros = new URLSearchParams(window.location.search);
    const idEditar = parametros.get('id');

    //Listar contenedores

    async function cargarContenedores() {

        if (!tablaContenedores) {
            return;
        }

        try {

            const respuesta = await fetch(`${API_BASE}/listar.php`, {
                method: 'GET',
                credentials: 'include'
            });

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                alert(datos.mensaje || 'Error al cargar los contenedores');
                return;
            }

            tablaContenedores.innerHTML = '';

            datos.data.forEach(contenedor => {

                const fila = document.createElement('tr');

                fila.innerHTML = `
                    <td>${contenedor.id_contenedor}</td>
                    <td>${contenedor.tipo_residuo}</td>
                    <td>${contenedor.capacidad}</td>
                    <td>${contenedor.ubicacion}</td>
                    <td>${contenedor.estado}</td>

                    <td>
                        <a
                            href="formContenedor.html?id=${contenedor.id_contenedor}"
                            class="btn-editar">
                            Editar
                        </a>

                        <button
                            type="button"
                            class="btn-eliminar"
                            data-id="${contenedor.id_contenedor}">
                            Eliminar
                        </button>
                    </td>
                `;

                tablaContenedores.appendChild(fila);
            });

        } catch (error) {

            console.error('Error:', error);

            alert('No se pudieron cargar los contenedores');
        }
    }

    //Eliminar contenedor

    if (tablaContenedores) {

        tablaContenedores.addEventListener('click', async (event) => {

            if (!event.target.classList.contains('btn-eliminar')) {
                return;
            }

            const id = event.target.dataset.id;

            const confirmar = confirm(
                '¿Seguro que deseas eliminar este contenedor?'
            );

            if (!confirmar) {
                return;
            }

            try {

                const respuesta = await fetch(`${API_BASE}/eliminar.php`, {
                    method: 'DELETE',

                    headers: {
                        'Content-Type': 'application/json'
                    },

                    credentials: 'include',

                    body: JSON.stringify({
                        id_contenedor: Number(id)
                    })
                });

                const datos = await respuesta.json();

                if (!respuesta.ok) {
                    alert(datos.mensaje || 'Error al eliminar el contenedor');
                    return;
                }

                alert(datos.mensaje);

                cargarContenedores();

            } catch (error) {

                console.error('Error:', error);

                alert('No se pudo eliminar el contenedor');
            }
        });
    }

    // Cargar datos para poder editar el contenedor

    async function cargarContenedorEditar() {

        if (!formContenedor || !idEditar) {
            return;
        }

        try {

            const respuesta = await fetch(`${API_BASE}/listar.php`, {
                method: 'GET',
                credentials: 'include'
            });

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                alert(datos.mensaje || 'Error al cargar el contenedor');
                return;
            }

            const contenedor = datos.data.find(
                item => Number(item.id_contenedor) === Number(idEditar)
            );

            if (!contenedor) {
                alert('Contenedor no encontrado');
                return;
            }

            document.getElementById('tipo_residuo').value =
                contenedor.tipo_residuo;

            document.getElementById('capacidad').value =
                contenedor.capacidad;

            document.getElementById('ubicacion').value =
                contenedor.ubicacion;

            document.getElementById('estado').value =
                contenedor.estado;


            const titulo = document.querySelector('.form-card h2');

            if (titulo) {
                titulo.textContent = 'Editar contenedor';
            }

        } catch (error) {

            console.error('Error:', error);

            alert('No se pudieron cargar los datos del contenedor');
        }
    }

    // Registro y edición

    if (formContenedor) {

        formContenedor.addEventListener('submit', async (event) => {

            event.preventDefault();

            const tipoResiduo =
                document.getElementById('tipo_residuo').value;

            const capacidad =
                document.getElementById('capacidad').value;

            const ubicacion =
                document.getElementById('ubicacion').value.trim();

            const estado =
                document.getElementById('estado').value;


            const contenedor = {
                tipo_residuo: tipoResiduo,
                capacidad: Number(capacidad),
                ubicacion: ubicacion,
                estado: estado
            };


            let url = `${API_BASE}/registrar.php`;
            let metodo = 'POST';


            if (idEditar) {

                url = `${API_BASE}/editar.php`;
                metodo = 'PUT';

                contenedor.id_contenedor = Number(idEditar);
            }


            try {

                const respuesta = await fetch(url, {

                    method: metodo,

                    headers: {
                        'Content-Type': 'application/json'
                    },

                    credentials: 'include',

                    body: JSON.stringify(contenedor)
                });


                const datos = await respuesta.json();


                if (!respuesta.ok) {

                    alert(
                        datos.mensaje ||
                        'Error al guardar el contenedor'
                    );

                    return;
                }


                alert(datos.mensaje);

                window.location.href = 'contenedores.html';


            } catch (error) {

                console.error('Error:', error);

                alert('No se pudo guardar el contenedor');
            }
        });
    }

    // Ejecutar según la página
    cargarContenedores();
    cargarContenedorEditar();

});