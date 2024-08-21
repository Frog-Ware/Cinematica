document.addEventListener("DOMContentLoaded", () => {
  const usuario = JSON.parse(localStorage.getItem("usuario"));
  const contenedor = document.getElementById("contenedorLogin");
  const ladoRegistro = document.getElementById("seccionRegistrar");
  const ladoIniciar = document.getElementById("seccionIniciar");
  //botones para cambiar de lado
  const cambiarLadoRegistro = document.getElementById("cambiarLadoRegistro");
  const cambiarLadoIniciar = document.getElementById("cambiarLadoIniciar");

  const abrirLogin = document.getElementById("abrirlogin");
  const botonCerrarLogin = document.querySelectorAll(".login__cerrar");
  const botonCerrarSesion = document.getElementById("cerrarSesion");

  let loginEstado = false;
  let estaEnIniciar = true;
  let anchoVentana = window.innerWidth;
  let alturaVentana = window.innerHeight;

  //Abrir y cerrar el login
  abrirLogin.addEventListener("click", desplegarLogin);
  botonCerrarLogin.forEach((e) => e.addEventListener("click", cerrarLogin));
  //cambiar entre register y login
  cambiarLadoRegistro.addEventListener("click", cambiarDeLado);
  cambiarLadoIniciar.addEventListener("click", cambiarDeLado);

  botonCerrarSesion.addEventListener("click", cerrarSesion);

  // Verifica el estado del usuario desde localStorage
  if (usuario) {
    console.log("Usuario logueado:", usuario);
    abrirLogin.style.display = "none";
    botonCerrarSesion.style.display = "block";

    esAdmin = usuario.email.endsWith("@cinematicaAdmin.com");
    console.log(
      `Usuario con ${
        esAdmin ? "privilegios de administrador" : "privilegios normales"
      }`
    );

    mostarLinkBackoffice();
  } else {
    console.log("No hay un usuario logueado.");
  }

  // Funcion que muestra el link al back office dependiendo si el usuario es administrador
  //si el usuario es admin crea un link en el sidebar, si es cliente lo borra.
  function mostarLinkBackoffice() {
    const ul = document.querySelector("#sideBar ul");

    if (esAdmin) {
      const backofficeLi = document.createElement("li");
      const backofficeLink = document.createElement("a");
      const backofficeButton = document.createElement("button");

      backofficeLink.href = "menuBackoffice.html";
      backofficeButton.className = "sideBar_btn btn_link";
      backofficeButton.textContent = "BACKOFFICE";

      backofficeLink.appendChild(backofficeButton);
      backofficeLi.appendChild(backofficeLink);
      backofficeLi.id = "backofficeLi";

      ul.appendChild(backofficeLi);
    } else {
      const backofficeLi = document.querySelector("#backofficeLi");

      if (backofficeLi) ul.removeChild(backofficeLi);
    }
  }

  // funcion que cierra la sesion
  function cerrarSesion() {
    localStorage.removeItem("usuario");
    abrirLogin.style.display = "block";
    botonCerrarSesion.style.display = "none";
    location.reload();
  }

  //Funciones para el despliege del login

  //despliega el login
  function desplegarLogin() {
    if (!loginEstado) {
      loginEstado = true;
      contenedor.style.display = "flex";

      const alturaCentrada = (alturaVentana - contenedor.offsetHeight) / 2;

      setTimeout(() => {
        contenedor.style.top = `${alturaCentrada}px`;
      }, 500);
    }
  }

  //cierra el login
  function cerrarLogin() {
    contenedor.style.top = "-100%";
    loginEstado = false;
    estaEnIniciar = true;
    ladoRegistro.style.zIndex = "0";
    limpiarCampos();
  }

  //cambia de lado entre iniciarSesión y registrarse
  function cambiarDeLado() {
    const tamañoMobile = 426;
    const tamañoTablet = 1000;

    const setStyles = (transform, iniciarIzq, registrarDer) => {
      ladoIniciar.style.transform = transform;
      ladoIniciar.style.left = iniciarIzq;
      ladoRegistro.style.left = registrarDer;
    };

    if (anchoVentana < tamañoMobile) {
      setStyles("scale(0.5)", "-57%", "20%");
    } else if (anchoVentana < tamañoTablet) {
      setStyles("scale(0.5)", "-40", "40%");
    } else {
      setStyles("", "-1%", "51%");
    }

    setTimeout(() => {
      if (estaEnIniciar) {
        ladoRegistro.style.zIndex = "5";
        estaEnIniciar = false;
      } else {
        ladoRegistro.style.zIndex = "3";
        estaEnIniciar = true;
      }
    }, 300);

    setTimeout(() => {
      if (anchoVentana < tamañoMobile) {
        setStyles("scale(1)", "0", "0");
      } else if (anchoVentana < tamañoTablet) {
        setStyles("scale(1)", "10%", "10%");
      } else {
        setStyles("", "25%", "25%");
      }
    }, 600);
  }
});
