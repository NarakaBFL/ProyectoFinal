function validarRegistro(){
    let nombre = document.getElementById("nombre").value;
    let email = document.getElementById("email").value;
    let pass = document.getElementById("pass").value;
    let passOK = document.getElementById("passOK").value;

    if (nombre === "" || email === "" || pass === "" || passOK === "") {
        alert("Tenés que completar todos los campos");
        return false;
    }

    if (pass.length < 8) {
        alert("La contraseña tiene que tener como mínimo 8 caracteres");
        return false;
    }

    if (pass !== passOK) {
        alert("Las contraseñas no coiniciden");
        return false;
    }
    return true;
}

function validarLogin(){
    let email = document.getElementById("email").value;
    let pass = document.getElementById("pass").value;

    if (email === "" || pass === "") {
        alert("Tenés que completar todos los campos");
        return false;
    }
    return true;
}