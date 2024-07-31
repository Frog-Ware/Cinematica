// botones para abrir el login y el sidebar
const sideBar = document.getElementById("sideBar");
const botonAbrir = document.getElementById("botonAbrir");
const cerrar = document.getElementById("cerrar");

let sideBarState = false; // estado del sidebar para saber si esta abierto o cerrado
const buscarPelicula = document.getElementById("buscarPelicula");
const PeliculaFiltro = document.getElementById("PeliculaFiltro");

// Función para abrir y cerrar el sidebar
// Al clickear el boton del menú el sidebar cambiara el valor de la propiedad left y cambiando el estado
// del sidebar
function sideBarFunction() {
  if (sideBarState == false) {
    sideBar.style.left = "-0px";
    sideBarState = true;
  } else {
    sideBar.style.left = "-281px";
    sideBarState = false;
  }
}

botonAbrir.onclick = (event) => {
  event.stopPropagation(); // Detiene la propagación del evento para evitar que cierre el sidebar inmediatamente
  sideBarFunction();
};

cerrar.onclick = () => sideBarFunction();

// Cierra el sidebar al hacer clic fuera de el
document.onclick = function (e) {
  if (e.target.id !== "sideBar" && e.target.id !== "botonAbrir") {
    sideBar.style.left = "-281px";
    sideBarState = false;
  }
};

//slider arriba

document.addEventListener("DOMContentLoaded", () => {
  //slider principal del home
  //primero establece una variable la cual se utilizara como indice y otra la cual almacena
  //la cantidad de imagenes o "slides", luego la funcion showSlides umenta el indice y si este supera
  //la cantidad total vuelve al 0, luego Aplica una transformación CSS a cada diapositiva
  //para moverla horizontalmente según el valor de offset.
  let slideIndex = 0;
  const slides = document.querySelectorAll(".slide");
  const totalSlides = slides.length;

  function showSlides() {
    slideIndex++;
    if (slideIndex >= totalSlides) {
      slideIndex = 0;
    }
    //Calcula el desplazamiento necesario para mostrar el slide actual
    const offset = -slideIndex * 100;
    slides.forEach((slide) => {
      slide.style.transform = `translateX(${offset}%)`;
    });
    setTimeout(showSlides, 3000); // llama a la función cada 3 segundos
  }
  showSlides();

  //Cartelera

  //este fetch trae lso datos de la cartelera, luego con estos datos crea las peliculas e inicia el carrusel
  fetch("assets/php/page/cartelera.php", {
    method: "POST",
  })
    .then((response) => response.json())
    .then((data) => {
      let peliculas = data.cartelera;
      console.log(data.cartelera);

      if (!peliculas || peliculas.length === 0) {
        console.log("cartelera vacía");
        return;
      }

      const contenedorPeliculas = document.getElementById("peliculas");
      peliculas.forEach((movie) => {
        const pelicula = document.createElement("img");
        pelicula.src = "assets/img/peliculas/" + movie.poster;
        contenedorPeliculas.appendChild(pelicula);
      });

      iniciarCarrucel();
    })
    .catch((error) => {
      console.error("Error al cargar los datos:", error);
    });

  function iniciarCarrucel() {
    const carrusel = document.querySelector(".carrusel");
    const arrowIcons = document.querySelectorAll(".cartelera i"); //botones de la cartelera
    const imagen = carrusel.querySelectorAll("img");

    if (imagen.length === 0) {
      console.log("No hay imágenes en el carrusel.");
      return;
    }

    const firstImg = imagen[0];
    //Calcula el ancho total del contenido desplazable "scrollWidth" menos el ancho visible del carrusel "clientWidth"
    // Muestra u oculta los íconos de las flechas dependiendo de la posición de desplazamiento "scrollLeft"
    // Si el carrusel está completamente a la izquierda, oculta la flecha izquierda.
    // Si el carrusel está completamente a la derecha, oculta la flecha derecha.
    const mostaryocultarBotones = () => {
      let scrollWidth = carrusel.scrollWidth - carrusel.clientWidth;
      arrowIcons[0].style.display =
        carrusel.scrollLeft === 0 ? "none" : "block";
      arrowIcons[1].style.display =
        carrusel.scrollLeft === scrollWidth ? "none" : "block";
    };

    //Calcula el ancho de la primera imagen más un margen de 400 píxeles (firstImgWidth).
    //Desplaza el carrusel a la izquierda o derecha según el ícono clicado
    arrowIcons.forEach((icon) => {
      icon.addEventListener("click", () => {
        let firstImgWidth = firstImg.clientWidth + 400;
        carrusel.scrollLeft +=
          icon.id === "left" ? -firstImgWidth : firstImgWidth;
        setTimeout(() => mostaryocultarBotones(), 60);
      });
    });
  }
});
