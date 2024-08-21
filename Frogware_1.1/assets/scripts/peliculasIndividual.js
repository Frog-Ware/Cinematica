const cuadroButacas = document.getElementById("cuadroButacas");
const b_etiqueta = document.getElementById("b_etiqueta");
const botonBuscarButaca = document.getElementById("botonEnviarCine");
const botonReservar = document.getElementById("botonReservar");
const butacasGuardadas = [];
let peliculaRecuperada = JSON.parse(localStorage.getItem('peliculas'));
const peliculaPoster = document.getElementById("peliculaPoster");
const peliculaCabecera = document.getElementById("peliculaCabecera");
const peliculaNombre = document.getElementById("peliculaNombre");
const peliculaSinopsis = document.getElementById("peliculaSinopsis");
const peliculaEtiquetas = document.getElementById("peliculaEtiquetas");

console.log(peliculaRecuperada);

peliculaPoster.innerHTML = `<img src="assets/img/peliculas/${peliculaRecuperada.poster}" alt="">`
peliculaCabecera.innerHTML =  `<img src="assets/img/peliculas/${peliculaRecuperada.cabecera}" alt=""></img>`
peliculaNombre.innerHTML = `<p>${peliculaRecuperada.nombrePelicula}</p>`
peliculaSinopsis.innerHTML = `<p>${peliculaRecuperada.sinopsis}</p>`
peliculaEtiquetas.innerHTML = 
 ` 
                <div>
                <div class="etiqueta bounce-in-fwd">Clasficación: ${peliculaRecuperada.pegi} 
                </div>
                <div class="etiqueta bounce-in-fwd">Director:${peliculaRecuperada.director}</div>
                </div>
                <div>
                <div class="etiqueta bounce-in-fwd">${peliculaRecuperada.nombreCategoria} </div>
                <div class="etiqueta bounce-in-fwd">Duración: ${peliculaRecuperada.duracion} </div>
                <div class="etiqueta bounce-in-fwd">Actores:${peliculaRecuperada.actores}</div>
                </div>`


document.addEventListener("DOMContentLoaded", () => {
  const playButton = document.getElementById("play");
  const cabeceraImg = document.querySelector(".cabecera img");

  playButton.addEventListener("mouseenter", () => {
    cabeceraImg.style.transform = "scale(1.3)";
  });

  playButton.addEventListener("mouseleave", () => {
    cabeceraImg.style.transform = "scale(1)";
  });
});

const cines = [
  { cine: "costa", sala1: "1", sala2: "2" },
  { cine: "chaplin", sala1: "2", sala2: "1" },
];

const disposicion1 = [
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 0, 0, 0, 0, 0],
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 0, 0, 0, 0, 0],
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0],
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0],
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0],
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0],
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1],
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1],
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1],
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1],
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1],
  [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
];

const disposicion2 = [
  [1, 1, 1, 1, 1, 0, 0, 2, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 0, 0, 2, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
];

let disposicionSeleccionada;

botonBuscarButaca.addEventListener("click", () => {
  const cineElejido = document.getElementById("cine").value;
  const salaElejida = document.getElementById("sala").value;

  disposicionSeleccionada = null;
  b_etiqueta.innerHTML = ` `;

  cines.forEach((cine) => {
    window.location.href = "#cuadroButacas";
    if (cineElejido == cine.cine) {
      console.log("CINE ENCONTRADO");
      if (salaElejida == cine.sala1) {
        disposicionSeleccionada = disposicion1;
      } else if (salaElejida == cine.sala2) {
        disposicionSeleccionada = disposicion2;
      }
    } else {
      console.log("cine no encontrado");
    }
  });
  console.log(disposicionSeleccionada);
  if (disposicionSeleccionada) {
    cuadroButacas.style.display = "flex";
    botonReservar.style.display = "Initial";
    crearButacas(
      disposicionSeleccionada.length,
      disposicionSeleccionada[0].length,
      disposicionSeleccionada
    );
  } else {
    alert("No ha seleccionado ningun cine o sala");
    console.error(
      "No se encontró una disposición para el cine y la sala seleccionados."
    );
    cuadroButacas.style.display = "none";
  }
});

function crearButacas(filas, butacasXFila, disposiciones) {
  cuadroButacas.innerHTML = "";

  for (let i = 0; i < filas; i++) {
    const fila = document.createElement("div");
    cuadroButacas.appendChild(fila);
    fila.classList.add("fila");
    fila.id = i + 1;

    const numFila = document.createElement("div");
    numFila.classList.add("num_fila");
    numFila.innerHTML = fila.id < 10 ? `${fila.id}&nbsp;&nbsp;` : `${fila.id}`;
    fila.appendChild(numFila);

    let butacaIndex = 1;
    for (let j = 0; j < butacasXFila; j++) {
      const butaca = document.createElement("div");
      const valor = disposiciones[i][j];
      if (valor === 1) {
        butaca.classList.add("butaca", "butaca--seleccionable");
        butaca.id = `${i + 1}-${butacaIndex}`;
        butacaIndex++;
      } else if (valor === 2) {
        butaca.classList.add("butaca", "butaca--especial");
        butaca.id = `${i + 1}-${butacaIndex}`;
        butacaIndex++;
      } else {
        butaca.classList.add("butaca", "butaca--oculta");
      }
      fila.appendChild(butaca);
    }
  }

  añadirEventosButaca();
}

function añadirEventosButaca() {
  let butacasOcupadas = 0;

  document
    .querySelectorAll(".butaca--seleccionable, .butaca--especial")
    .forEach((element) => {
      element.addEventListener("click", () => {
        const id = element.id;
        let indexGuardar = butacasGuardadas.indexOf(id);
        if (!element.classList.contains("butaca--seleccionada")) {
          console.log(`Butaca ${id} seleccionada.`);
          element.classList.add("butaca--seleccionada");
          butacasOcupadas++;
          butacasGuardadas.push(id);
          console.log(butacasGuardadas);
          if (butacasOcupadas <= 10) {
            b_etiqueta.innerHTML += `<div id="b_etiqueta_${id}" class="b_etiqueta">F${id}</div>`;
          } else {
            alert("Solo puedes seleccionar 10 butacas");
            butacasOcupadas--;
            element.classList.remove("butaca--seleccionada");
          }
        } else {
          console.log(`Butaca ${id} deseleccionada.`);
          element.classList.remove("butaca--seleccionada");
          butacasOcupadas--;
          if (indexGuardar !== -1) {
            butacasGuardadas.splice(indexGuardar, 1);
            console.log(butacasGuardadas);
          }
          const elementoButaca = document.getElementById(`b_etiqueta_${id}`);
          if (elementoButaca) {
            elementoButaca.remove();
          }
        }
      });
    });
}

document.addEventListener("DOMContentLoaded", () => {
  const playButton = document.getElementById("play");
  const trailer = document.getElementById("videoTrailer");
  const botonCerrar = trailer.querySelector(".cerrar");
  const iframe = trailer.querySelector("iframe");

  let idTrailer = peliculaRecuperada.trailer;
  let validId = idTrailer.split("=");
  const videoURL = `https://www.youtube.com/embed/${validId[1]}?autoplay=1`;
  console.log(peliculaRecuperada.trailer);
  // https://www.youtube.com/watch?v=eOrNdBpGMv8

  playButton.addEventListener("click", () => {
    iframe.src = videoURL;
    trailer.style.display = "block";
  });

  botonCerrar.addEventListener("click", () => {
    trailer.style.display = "none";
    iframe.src = "";
  });

  window.addEventListener("click", (event) => {
    if (event.target === trailer) {
      trailer.style.display = "none";
      iframe.src = "";
    }
  });
});

botonReservar.addEventListener("click", () => {
  localStorage.setItem("butacasGuardadas", JSON.stringify(butacasGuardadas));
  window.location.href = "productos.html";
});
