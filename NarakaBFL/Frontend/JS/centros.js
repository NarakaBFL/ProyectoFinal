// ==========================================================
// SiGeRU - Centros de Acopio y Vertederos
// Consume la API REST: Backend/api/centros/
// ==========================================================

// Ajustar según dónde esté desplegado el Backend
const API_BASE = 'http://localhost/SiGeRU/Backend/api/centros';

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('tablaCentros')) {
        inicializarListado();
    }
    if (document.getElementById('formCentro')) {
        inicializarFormulario();
    }
});

// ---------------------------------------------------------
// LISTADO (centros.html)
// ---------------------------------------------------------
function inicializarListado() {
    cargarCentros();

    document.getElementById('filtros').addEventListener('submit', (e) => {
        e.preventDefault();
        cargarCentros();
    });
}

async function cargarCentros() {
    const ubicacion = document.getElementById('f_ubicacion').value.trim();
    const tipo_residuo = document.getElementById('f_tipo_residuo').value;
    const estado = document.getElementById('f_estado').value;

    const params = new URLSearchParams();
    if (ubicacion) params.append('ubicacion', ubicacion);
    if (tipo_residuo) params.append('tipo_residuo', tipo_residuo);
    if (estado) params.append('estado', estado);

    try {
        const resp = await fetch(`${API_BASE}/listar.php?${params.toString()}`);
        const json = await resp.json();

        if (json.status !== 'ok') {
            alert(json.message || 'Error al cargar los centros de acopio');
            return;
        }

        renderTabla(json.data);
    } catch (err) {
        console.error(err);
        alert('No se pudo conectar con el servidor');
    }
}

function renderTabla(centros) {
    const cuerpo = document.getElementById('cuerpoTabla');
    const mensajeVacio = document.getElementById('mensajeVacio');
    cuerpo.innerHTML = '';

    if (!centros || centros.length === 0) {
        mensajeVacio.style.display = 'block';
        return;
    }
    mensajeVacio.style.display = 'none';

    centros.forEach(c => {
        const fila = document.createElement('tr');

        const alerta = c.porcentaje_ocupacion >= 80 ? ' ⚠️' : '';

        fila.innerHTML = `
            <td>${escapeHtml(c.nombre)}</td>
            <td>${escapeHtml(c.ubicacion)}</td>
            <td>${escapeHtml(c.tipo_residuo)}</td>
            <td>${c.capacidad_total}</td>
            <td>${c.ocupacion_actual}</td>
            <td>${c.porcentaje_ocupacion}%${alerta}</td>
            <td>${c.estado}</td>
            <td>
                <a href="formCentro.html?id=${c.id_centro}">Editar</a>
                ${c.estado === 'Activo'
                    ? `<button onclick="eliminarCentro(${c.id_centro})">Baja</button>`
                    : ''}
            </td>
        `;
        cuerpo.appendChild(fila);
    });
}

async function eliminarCentro(id) {
    if (!confirm('¿Confirma dar de baja este centro de acopio?')) return;

    try {
        const resp = await fetch(`${API_BASE}/eliminar.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_centro: id })
        });
        const json = await resp.json();

        if (json.status !== 'ok') {
            alert(json.message || 'Error al dar de baja el centro');
            return;
        }

        cargarCentros();
    } catch (err) {
        console.error(err);
        alert('No se pudo conectar con el servidor');
    }
}

// ---------------------------------------------------------
// FORMULARIO (formCentro.html) - Alta y Edición
// ---------------------------------------------------------
function inicializarFormulario() {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');

    if (id) {
        document.getElementById('tituloForm').textContent = 'Editar Centro de Acopio';
        document.getElementById('grupoOcupacion').style.display = 'block';
        document.getElementById('id_centro').value = id;
        cargarDatosCentro(id);
    }

    document.getElementById('formCentro').addEventListener('submit', guardarCentro);
}

async function cargarDatosCentro(id) {
    try {
        const resp = await fetch(`${API_BASE}/listar.php`);
        const json = await resp.json();
        const centro = (json.data || []).find(c => c.id_centro == id);

        if (!centro) {
            mostrarError('Centro de acopio no encontrado');
            return;
        }

        document.getElementById('nombre').value = centro.nombre;
        document.getElementById('ubicacion').value = centro.ubicacion;
        document.getElementById('tipo_residuo').value = centro.tipo_residuo;
        document.getElementById('capacidad_total').value = centro.capacidad_total;
        document.getElementById('ocupacion_actual').value = centro.ocupacion_actual;
    } catch (err) {
        console.error(err);
        mostrarError('No se pudo conectar con el servidor');
    }
}

async function guardarCentro(e) {
    e.preventDefault();
    ocultarError();

    const id_centro = document.getElementById('id_centro').value;
    const esEdicion = !!id_centro;

    const datos = {
        nombre: document.getElementById('nombre').value.trim(),
        ubicacion: document.getElementById('ubicacion').value.trim(),
        tipo_residuo: document.getElementById('tipo_residuo').value,
        capacidad_total: parseFloat(document.getElementById('capacidad_total').value)
    };

    if (esEdicion) {
        datos.id_centro = parseInt(id_centro);
        datos.ocupacion_actual = parseFloat(document.getElementById('ocupacion_actual').value || 0);
    }

    const url = esEdicion ? `${API_BASE}/modificar.php` : `${API_BASE}/registrar.php`;
    const metodo = esEdicion ? 'PUT' : 'POST';

    try {
        const resp = await fetch(url, {
            method: metodo,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });
        const json = await resp.json();

        if (json.status !== 'ok') {
            mostrarError(json.message || 'Error al guardar el centro de acopio');
            return;
        }

        if (json.data && json.data.alerta_capacidad) {
            alert('Atención: el centro superó el 80% de su capacidad');
        }

        window.location.href = 'centros.html';
    } catch (err) {
        console.error(err);
        mostrarError('No se pudo conectar con el servidor');
    }
}

// ---------------------------------------------------------
// Utilidades
// ---------------------------------------------------------
function mostrarError(msg) {
    const p = document.getElementById('mensajeError');
    p.textContent = msg;
    p.style.display = 'block';
}

function ocultarError() {
    const p = document.getElementById('mensajeError');
    p.style.display = 'none';
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
