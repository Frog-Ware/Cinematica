let ultimaLlamada = 0;

function enviarDatosRegistro() {
  const ahora = new Date().getTime();

  // Verifica si han pasado menos de 10 segundos desde la última llamada
  if (ahora - ultimaLlamada < 10000) {
    console.log("Solo se puede llamar una vez cada 10 segundos.");
    return;
  }

  // Si ha pasado más de 10 segundos, actualizar el tiempo de la última llamada
  ultimaLlamada = ahora;

  // Crear FormData con los datos del registro
  let datos = new FormData();
  datos.append("nombre", nombreRegistro.value);
  datos.append("apellido", apellidoRegistro.value);
  datos.append("email", emailRegistro.value);
  datos.append("passwd", passwrdRegistro.value);
  datos.append("numeroCelular", telefonoRegistro.value);
  datos.append("imagenPerfil", "imagen");

  // Enviar los datos mediante fetch
  fetch("assets/php/session/registro.php", {
    method: "POST",
    body: datos,
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.error) {
        console.log("Registro válido");

        alert(
          "Este es tu Token sin el no podras cambiar la contraseña: " +
            data.token
        );
      } else {
        switch (data.error) {
          case 1:
            console.log("Algo salió mal");
            break;
          case 2:
            mostrarError(mensajesError.usuarioExistente);
            break;
          case 3:
            mostrarError(mensajesError.camposVacios);
            break;
          case 4:
            console.log("Variable no asignada");
            break;
          default:
            console.log("Error desconocido");
        }
      }
    });
}
function enviarDatosIniciarSesion() {
  let datos = new FormData();
  datos.append("email", correoIniciarSesion.value);
  datos.append("passwd", passwdIniciarSesion.value);
  console.log(correoIniciarSesion.value);
  console.log(passwdIniciarSesion.value);

  fetch("assets/php/session/iniciarSesion.php", {
    method: "POST",
    body: datos,
  })
    .then((response) => {
      return response.text().then((texto) => {
        try {
          return JSON.parse(texto);
        } catch (error) {
          console.error("Error al analizar JSON:", error.message);
          throw new Error("La respuesta no es un JSON válido: " + texto);
        }
      });
    })
    .then((data) => {
      console.log(data);
      if (!data.error || data.error == 5) {
        console.log("Sesión iniciada con éxito");
        let infoUsuario = data.datosUsuario;
        localStorage.setItem("usuario", JSON.stringify(infoUsuario));
        location.reload();
      } else {
        switch (data.error) {
          case 1:
            mostrarErrorInicio("Credenciales no coinciden");
            break;
          case 2:
            mostrarErrorInicio("El email no se encuentra registrada");
            break;
          case 3:
            console.log("Al menos un campo está vacío");
            break;
          case 4:
            console.log("Al menos un campo no está asignado.");
            break;
          default:
            console.log("Error desconocido");
        }
      }
    })
    .catch((error) => console.error("Error:", error));
}
