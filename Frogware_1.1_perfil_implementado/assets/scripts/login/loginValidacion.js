// Campos de registro
const nombreRegistro = document.getElementById("nombreRegistrar");
const apellidoRegistro = document.getElementById("apellidoRegistrar");
const emailRegistro = document.getElementById("emailRegistrar");
const passwrdRegistro = document.getElementById("passwdRegistrar");
const confirmpasswd = document.getElementById("passwdConfirmar");
const telefonoRegistro = document.getElementById("telefonoRegistrar");
const botonEnviarRegistro = document.getElementById("botonEnviarRegistrar");

// Campos de inicio de sesión
const correoIniciarSesion = document.getElementById("correoIniciarSesion");
const passwdIniciarSesion = document.getElementById("passwdIniciarSesion");
const botonEnviarIniciarSesion = document.getElementById(
  "botonEnviarIniciarSesion"
);

// Caracteres que no podrán ser ingresados en ninguno de los campos
const caracteresInvalidos = /[<>$#/*"-'?'ª!=%&]/g;

const camposRegistrar = [
  nombreRegistro,
  apellidoRegistro,
  emailRegistro,
  passwrdRegistro,
  telefonoRegistro,
  confirmpasswd,
];
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

//abarca toda la funcionalidad de inicio de sesión y validación de campos
botonEnviarIniciarSesion.addEventListener("click", () => {
  //Utiliza el método every para comprobar que todos los campos no estén vacíos usando la función estaVacio
  // y que cumplan con los requisitos de caracteres usando verificarCaracteres
  const noEstaVacio = camposIniciar.every((campo) => !estaVacio(campo));
  const estaVerificado = camposIniciar.every((campo) =>
    verificarCaracteres(campo)
  );

  if (!noEstaVacio) {
    mostrarErrorInicio(mensajesError.camposVacios);
    return;
  }

  if (!estaVerificado) {
    mostrarErrorInicio(mensajesError.caracteresInvalidos);
    return;
  }

  if (!correoValido(correoIniciarSesion.value)) {
    mostrarErrorInicio(mensajesError.emailInvalido);
    return;
  }

  enviarDatosIniciarSesion();
  console.log("enviado");
});

// Esta parte abarca toda la funcionalidad de registro de usuario y validación de campos
botonEnviarRegistro.addEventListener("click", () => {
  const noEstaVacio = camposRegistrar.every((campo) => !estaVacio(campo));
  const estaVerificado = camposRegistrar.every((campo) =>
    verificarCaracteres(campo)
  );

  if (!noEstaVacio) {
    mostrarError(mensajesError.camposVacios);
    return;
  }

  if (!estaVerificado) {
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

  if (!correoValido(emailRegistro.value)) {
    mostrarError(mensajesError.emailInvalido);
    return;
  }
  mensajeValido("Tu registro realizado con éxito");
  enviarDatosRegistro();
});


// Funciones de validación y manejo de errores
const verificarCaracteres = e => !e.value.match(caracteresInvalidos);

const estaVacio = e => e.value === "";

const minCaracteresPasswd = passwd => passwd.value.length >= 8 && passwd.value.length <= 12;

//valida que el correo ingresado sea de tipo string, luego que no haya mas de 2 @
//y por ultimo valida que el dominio del correo sea valido.
//el ? en ?toLowerCase(); evita que si el dominio es null de un error al intentar aplicar el metodo
function correoValido(correo) {
  if (typeof correo !== "string") return false;

  const partes = correo.split("@");
  if (partes.length !== 2) return false;

  const dominio = partes[1]?.toLowerCase();

  if (
    dominio !== "gmail.com" &&
    dominio !== "outlook.com" &&
    dominio !== "hotmail.com" &&
    dominio !== "cinematicaadmin.com"
  ) {
    return false;
  }

  return true;
}

//funcion a cambiar por ahora sirve para pruebas.
function verificarTelefono(tel) {
  if (tel.value.length >= 9 && !isNaN(tel.value)) {
    return true;
  } else {
    return false;
  }
}

//cambian los estilos de los botones enviar pra informar al usuario un error
function cambiarEstiloDelBoton(boton,colorTexto, colorFondo, texto, tamanoFuente){
  boton.style.color = colorTexto;
  boton.style.backgroundColor = colorFondo;
  boton.value = texto;
  boton.style.fontSize = tamanoFuente;
}

function botonDefault(boton){
  cambiarEstiloDelBoton(boton, "#00EDFF", "#006794", "Enviar", "15px");
}
//estas funciones pueden fucionarse pero por ahora preferimos dejarlas así 
function mostrarError(mensaje) {
  console.log(mensaje);
  cambiarEstiloDelBoton(botonEnviarRegistro, "#ffffff", "#944040", mensaje, "12px");
  setTimeout(() => {
    botonDefault(botonEnviarRegistro);
  }, 2000);
}

function mostrarErrorInicio(mensaje) {
  console.log(mensaje);
  cambiarEstiloDelBoton(botonEnviarIniciarSesion, "#ffffff", "#944040", mensaje, "12px");
  setTimeout(() => {
    botonDefault(botonEnviarIniciarSesion);
  }, 2000);
}


function mensajeValido(mensaje) {
  console.log(mensaje);
  cambiarEstiloDelBoton( botonEnviarRegistro, "#ffffff", "#46FF00", mensaje, "12px");
  setTimeout(() => {
    botonDefault();
  }, 2000);
}

function limpiarCampos(){
  camposIniciar.forEach((campo) => campo.value = "");
  camposRegistrar.forEach((campo) => campo.value = "");
}