const form = document.getElementById('registroForm');
const mensaje = document.getElementById('mensaje');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const nombre = document.getElementById('nombre').value.trim();
    const apellido = document.getElementById('apellido').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;

    if (password !== confirmPassword) {
        mensaje.textContent = 'Las contraseñas no coinciden';
        return;
    }

    try {
        const res = await fetch('../../Backend/api/auth/registro.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                credentials: 'include',
                body: JSON.stringify({ nombre, apellido, email, password })
        });

        const data = await res.json();

         mensaje.textContent = data.mensaje;

        if (res.ok) {
            window.location.href = 'login.html';
        }

    } catch (error) {
        mensaje.textContent = 'Error al conectar con el servidor';
    }
});