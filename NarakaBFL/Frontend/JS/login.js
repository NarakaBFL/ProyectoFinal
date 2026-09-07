const form = document.getElementById('loginForm');
const mensaje = document.getElementById('mensaje');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    try {
        const res = await fetch('../../Backend/api/auth/login.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                credentials: 'include',
                body: JSON.stringify({ email, password })
        });

        const data = await res.json();

        if (!res.ok) {
            mensaje.textContent = data.mensaje;
            return;
        }

        const paneles = {
            1: 'panelVecino.html',
            2: 'panelAdmin.html',
            3: 'panelOperario.html',
            4: 'panelCuadrilla.html'
        };

        window.location.href = paneles[data.data.id_rol];

    } catch (error) {
        mensaje.textContent = 'Error al conectar con el servidor';
    }
});