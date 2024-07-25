// Campos de registro
const nombreRegistro = document.getElementById('nombreRegistrar');
const apellidoRegistro = document.getElementById('apellidoRegistrar');
const emailRegistro = document.getElementById('emailRegistrar');
const passwrdRegistro = document.getElementById('passwdRegistrar');
const confirmpasswd = document.getElementById('passwdConfirmar');
const telefonoRegistro = document.getElementById('telefonoRegistrar');
const botonEnviarRegistro = document.getElementById('botonEnviarRegistrar');

// Campos de inicio de sesión
const correoIniciarSesion = document.getElementById('correoIniciarSesion');
const passwdIniciarSesion = document.getElementById('passwdIniciarSesion');
const botonEnviarIniciarSesion = document.getElementById('botonEnviarIniciarSesion');

// Caracteres que no podrán ser ingresados en ninguno de los campos
const caracteresInvalidos = /[<>$#/*"-'?'ª!=%&]/g;

const camposRegistrar = [nombreRegistro, apellidoRegistro, emailRegistro, passwrdRegistro, telefonoRegistro];
const camposIniciar = [correoIniciarSesion, passwdIniciarSesion];

const mensajesError = {
    camposVacios: "Uno o más campos están vacíos",
    caracteresInvalidos: "Solo se puede ingresar caracteres alfanuméricos o '_'",
    contrasenasNoCoinciden: "Las contraseñas no coinciden",
    contrasenaCorta: "La contraseña debe tener al menos 8 caracteres",
    telefonoInvalido: "El número de celular no es válido",
    emailInvalido: "Debe introducir una dirección de correo válida",
    usuarioExistente: "Ya existe un usuario con ese email",
};


botonEnviarIniciarSesion.addEventListener("click", () => {
    const noEstaVacio = camposIniciar.every(campo => !estaVacio(campo));
    const estaVerificado = camposIniciar.every(campo => verificarCaracteres(campo));

    if (!noEstaVacio) {
        mostrarErrorInicio(mensajesError.camposVacios);
        return;
    }

    if (!estaVerificado){
        mostrarErrorInicio(mensajesError.caracteresInvalidos);
        return;
    }

    if (!correoValido(correoIniciarSesion.value)){
        mostrarErrorInicio(mensajesError.emailInvalido);
        return;
    }

    enviarDatosIniciarSesion();
    console.log('enviado')
});



// Esta parte abarca toda la funcionalidad de registro de usuario y validación de campos
botonEnviarRegistro.addEventListener("click", () => {
    const noEstaVacio = camposRegistrar.every(campo => !estaVacio(campo));
    const estaVerificado = camposRegistrar.every(campo => verificarCaracteres(campo));

    if (!noEstaVacio) {
        mostrarError(mensajesError.camposVacios);
        return;
    }

    if (!estaVerificado){
        mostrarError(mensajesError.caracteresInvalidos);
        return;
    }

    if (passwrdRegistro.value !== confirmpasswd.value) {
        mostrarError(mensajesError.contrasenasNoCoinciden);
        return;
    }

    if (!minCaracteresPasswd(passwrdRegistro)) {
        mostrarError(mensajesError.contrasenaCorta);
        return;
    }

    if (!verificarTelefono(telefonoRegistro)) {
        mostrarError(mensajesError.telefonoInvalido);
        return;
    }

    if (!correoValido(emailRegistro.value)){
        mostrarError(mensajesError.emailInvalido);
        return;
    }
    mensajeValido("Tu registro realizado con éxito");
    enviarDatosRegistro();
});

// Funciones de validación y manejo de errores
function verificarCaracteres(e) {
    const elemento = e.value;
    if (elemento.match(caracteresInvalidos)) {
    
        return false;
    } else {
        
        return true;
    }
}

function estaVacio(e) {
    if (e.value === '') return true;
    return false;
}

function correoValido(correo) {
    if (typeof correo !== "string") return false;
    
    const partes = correo.split("@");
    if (partes.length !== 2) return false;

    const dominio = partes[1]; 

    if (dominio !== "gmail.com" && dominio !== "outlook.com" && dominio !== "hotmail.com" && dominio !== "cinematicaAdmin.com") {
        return false;
    }
    
    return true;
}

function verificarTelefono(tel) {
    if (tel.value.length >= 9 && !isNaN(tel.value)) {
        return true;
    } else {
        return false;
    }
}

function minCaracteresPasswd(passwd) {
    if (passwd.value.length >= 8 && passwd.value.length <= 12) {
        return true;
    } else {
        return false;
    }
}

function mostrarError(mensaje) {
    console.log(mensaje);
    botonEnviarRegistro.style.color = '#ffffff';
    botonEnviarRegistro.style.backgroundColor = '#944040';
    botonEnviarRegistro.value = mensaje;
    botonEnviarRegistro.style.fontSize = "12px";
    setTimeout(() => {
        botonEnviarRegistro.style.color = '#00EDFF';
        botonEnviarRegistro.style.backgroundColor = '#006794';
        botonEnviarRegistro.value = "Enviar";
        botonEnviarRegistro.style.fontSize = "15px";
    }, 2000);
}

function mensajeValido(mensaje){
    botonEnviarRegistro.style.color = '#ffffff';
    botonEnviarRegistro.style.backgroundColor = '#46FF00';
    botonEnviarRegistro.value = mensaje;
    botonEnviarRegistro.style.fontSize = "12px";
    setTimeout(() => {
        botonEnviarRegistro.style.color = '#00EDFF';
        botonEnviarRegistro.style.backgroundColor = '#006794';
        botonEnviarRegistro.value = "Enviar";
        botonEnviarRegistro.style.fontSize = "15px";
    }, 2000);
}

function mostrarErrorInicio(mensaje) {
    console.log(mensaje);
    botonEnviarIniciarSesion.style.color = '#ffffff';
    botonEnviarIniciarSesion.style.backgroundColor = '#944040';
    botonEnviarIniciarSesion.value = mensaje;
    botonEnviarIniciarSesion.style.fontSize = "12px";
    setTimeout(() => {
        botonEnviarIniciarSesion.style.color = '#00EDFF';
        botonEnviarIniciarSesion.style.backgroundColor = '#006794';
        botonEnviarIniciarSesion.value = "Enviar";
        botonEnviarIniciarSesion.style.fontSize = "15px";
    }, 2000);
}

function limpiarCampos(){
    nombreRegistro.value = "";
    apellidoRegistro.value = ""; 
    emailRegistro.value = "";
    passwrdRegistro.value = ""; 
    confirmpasswd.value = "";
    telefonoRegistro.value = ""; 
    botonEnviarRegistro.value = "";
    correoIniciarSesion.value = ""; 
    passwdIniciarSesion.value = ""; 
}
