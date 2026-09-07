const rolesPermitidos = document.body.dataset.roles
.split(',')
.map(Number);

const paneles = {
    1: 'panelVecino.html',
    2: 'panelAdmin.html',
    3: 'panelOperario.html',
    4: 'panelCuadrilla.html'
};

(async () => {
    try {
        const res = await fetch('../../Backend/api/auth/sesion.php', {
            credentials: 'include'
        });

        if (!res.ok) {
            window.location.href = 'login.html';
            return;
        }

        const data = await res.json();
        const rol = Number(data.data.id_rol);

        if (!rolesPermitidos.includes(rol)) {
            window.location.href = paneles[rol];
        }

    }catch (error) {
        window.location.href = 'login.html';
    }
})();