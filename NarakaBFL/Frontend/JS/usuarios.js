const API_BASE = '../../Backend/api/usuarios';

const tablaUsuarios = document.getElementById('tablaUsuarios');
const formUsuario = document.getElementById('formUsuario');

if (tablaUsuarios) {
    cargarUsuarios();
}

if (formUsuario) {
    inicializarFormulario();
}


async function cargarUsuarios() {

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
            alert(datos.mensaje || 'Error al cargar los usuarios');
            return;
        }

        mostrarUsuarios(datos.data);

    } catch (error) {

        console.error(error);
        alert('Error de conexión con el servidor');
    }
}


function mostrarUsuarios(usuarios) {

    const mensajeVacio =
        document.getElementById('mensajeVacio');

    tablaUsuarios.innerHTML = '';

    if (!usuarios || usuarios.length === 0) {

        mensajeVacio.style.display = 'block';
        return;
    }

    mensajeVacio.style.display = 'none';

    usuarios.forEach(usuario => {

        const estado =
            Number(usuario.activo) === 1
                ? 'Activo'
                : 'Inactivo';

        const fila =
            document.createElement('tr');

        fila.innerHTML = `
            <td>${usuario.id_usuario}</td>
            <td>${usuario.nombre}</td>
            <td>${usuario.apellido}</td>
            <td>${usuario.email}</td>
            <td>${usuario.rol}</td>
            <td>${estado}</td>

            <td>
                <a
                    href="formUsuario.html?id=${usuario.id_usuario}"
                    class="btn-editar"
                >
                    Editar
                </a>

                ${
                    Number(usuario.activo) === 1
                        ? `
                            <button
                                class="btn-eliminar"
                                onclick="eliminarUsuario(${usuario.id_usuario})"
                            >
                                Dar de baja
                            </button>
                        `
                        : ''
                }
            </td>
        `;

        tablaUsuarios.appendChild(fila);
    });
}


async function eliminarUsuario(id) {

    const confirmar = confirm(
        '¿Está seguro que desea dar de baja este usuario?'
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
                    id_usuario: id
                })
            }
        );

        const datos = await respuesta.json();

        if (!respuesta.ok) {
            alert(
                datos.mensaje ||
                'Error al dar de baja el usuario'
            );
            return;
        }

        alert(datos.mensaje);

        cargarUsuarios();

    } catch (error) {

        console.error(error);
        alert('Error de conexión con el servidor');
    }
}


function inicializarFormulario() {

    const parametros =
        new URLSearchParams(window.location.search);

    const id = parametros.get('id');

    if (id) {

        document.getElementById('tituloForm').textContent =
            'Editar Usuario';

        document.getElementById('descripcionForm').textContent =
            'Modifique los datos del usuario. Deje la contraseña vacía para mantener la actual.';

        document.getElementById('grupoEstado').style.display =
            'block';

        document.getElementById('id_usuario').value =
            id;

        cargarDatosUsuario(id);

    } else {

        document.getElementById('contrasena').required =
            true;

        document.getElementById('confirmarContrasena').required =
            true;
    }

    formUsuario.addEventListener(
        'submit',
        guardarUsuario
    );
}


async function cargarDatosUsuario(id) {

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

        const usuario = datos.data.find(
            usuario => usuario.id_usuario == id
        );

        if (!usuario) {
            mostrarError('Usuario no encontrado');
            return;
        }

        document.getElementById('nombre').value =
            usuario.nombre;

        document.getElementById('apellido').value =
            usuario.apellido;

        document.getElementById('email').value =
            usuario.email;

        document.getElementById('id_rol').value =
            usuario.id_rol;

        document.getElementById('activo').value =
            usuario.activo;

    } catch (error) {

        console.error(error);

        mostrarError(
            'Error de conexión con el servidor'
        );
    }
}


async function guardarUsuario(evento) {

    evento.preventDefault();

    ocultarError();

    const idUsuario =
        document.getElementById('id_usuario').value;

    const esEdicion =
        idUsuario !== '';

    const contrasena =
        document.getElementById('contrasena').value;

    const confirmarContrasena =
        document.getElementById(
            'confirmarContrasena'
        ).value;

    if (contrasena !== confirmarContrasena) {
        mostrarError(
            'Las contraseñas no coinciden'
        );
        return;
    }

    const datos = {
        nombre:
            document.getElementById('nombre').value.trim(),

        apellido:
            document.getElementById('apellido').value.trim(),

        email:
            document.getElementById('email').value.trim(),

        id_rol:
            Number(
                document.getElementById('id_rol').value
            )
    };

    if (contrasena !== '') {
        datos.contrasena = contrasena;
    }

    if (esEdicion) {

        datos.id_usuario =
            Number(idUsuario);

        datos.activo =
            Number(
                document.getElementById('activo').value
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
                'Error al guardar el usuario'
            );

            return;
        }

        window.location.href =
            'usuarios.html';

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

    elemento.textContent =
        mensaje;
}


function ocultarError() {

    const elemento =
        document.getElementById('mensajeError');

    elemento.textContent =
        '';
}