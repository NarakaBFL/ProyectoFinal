const tabla = document.getElementById('tablaMaquinaria');

const estadosTexto = {
    disponible: 'Disponible',
    en_uso: 'En uso',
    mantenimiento: 'Mantenimiento',
    fuera_de_servicio: 'Fuera de servicio'};

    async function cargarMaquinaria() {
        try{
            const res = await fetch(
                '../../Backend/api/maquinaria/listar.php',
            {
                credentials: 'include'
            }
        );

        const data = await res.json();

        tabla.innerHTML = '';

        data.data.forEach(maquina => {
            tabla.innerHTML += `
            <tr>
                <td>${maquina.id_maquinaria}</td>
                <td>${maquina.tipo}</td>
                <td>${estadosTexto[maquina.estado] ?? maquina.estado}</td>
                <td>${maquina.fecha_ultimo_mantenimiento ?? 'Sin registro'}</td>
                <td>${maquina.centro}</td>
                <td>
                <a href="formMaquinaria.html?id=${maquina.id_maquinaria}" 
                class="btn-editar">
                Editar
                </a>

                <button class="btn-eliminar" 
                onclick="eliminarMaquinaria(${maquina.id_maquinaria})">
                Eliminar
                </button>
                </td>
            </tr>

            `;
        });

        } catch (error) {
            console.error('Error al cargar maquinaria:', error);
        }
    }

    cargarMaquinaria();

async function eliminarMaquinaria(id) {

    const confirmar = confirm('¿Seguro que desea eliminar esta maquinaria?');

    if (!confirmar) {
        return;
    }

    try {
         const res = await fetch(
            '../../Backend/api/maquinaria/eliminar.php',
         {
            method: 'DELETE',

            headers: {
                'Content-Type': 'application/json'
            },

            credentials: 'include',

            body: JSON.stringify({
                id_maquinaria: id
            })
         }
      );

      const data = await res.json();

      alert(data.mensaje);

      if (res.ok) {
        cargarMaquinaria();
      }

   } catch (error) {

    console.error('Error al eliminar maquinaria', error);
   }
}