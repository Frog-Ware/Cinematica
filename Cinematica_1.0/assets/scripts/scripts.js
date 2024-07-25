const sideBar = document.getElementById("sideBar");
const botonAbrir = document.getElementById("botonAbrir");
const cerrar = document.getElementById("cerrar");
let sideBarState = false;
const buscarPelicula = document.getElementById("buscarPelicula");
const PeliculaFiltro = document.getElementById("PeliculaFiltro");

// Función para abrir y cerrar el sidebar
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

document.addEventListener('DOMContentLoaded', () => {
    let slideIndex = 0;
    const slides = document.querySelectorAll('.slide');
    const totalSlides = slides.length;

    function showSlides() {
        slideIndex++;
        if (slideIndex >= totalSlides) {
            slideIndex = 0;
        }
        const offset = -slideIndex * 100;
        slides.forEach(slide => {
            slide.style.transform = `translateX(${offset}%)`;
        });
        setTimeout(showSlides, 3000); // Cambia de imagen cada 3 segundos
    }

    showSlides();


//slider cartelera

    fetch('assets/php/page/cartelera.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        let peliculas = data.cartelera;
        console.log(data.cartelera);

        if (!peliculas || peliculas.length === 0) {
            console.log('cartelera vacía');
            return;
        }

        const contenedorPeliculas = document.getElementById('peliculas');
        peliculas.forEach(movie => {
            const pelicula = document.createElement('img');
            pelicula.src = 'assets/img/peliculas/' + movie.poster;
            contenedorPeliculas.appendChild(pelicula);
        });

        iniciarCarrucel();
    })
    .catch(error => {
        console.error('Error al cargar los datos:', error);
    });

    function iniciarCarrucel() {
        const carrusel = document.querySelector(".carrusel");
        const arrowIcons = document.querySelectorAll(".cartelera i");
        const imagen = carrusel.querySelectorAll("img");
        
        if (imagen.length === 0) {
            console.log('No hay imágenes en el carrusel.');
            return;
        }

        const firstImg = imagen[0];

        const mostaryocultarBotones = () => {
            let scrollWidth = carrusel.scrollWidth - carrusel.clientWidth; 
            arrowIcons[0].style.display = carrusel.scrollLeft === 0 ? "none" : "block";
            arrowIcons[1].style.display = carrusel.scrollLeft === scrollWidth ? "none" : "block";
        };

        arrowIcons.forEach(icon => {
            icon.addEventListener("click", () => {
                let firstImgWidth = firstImg.clientWidth + 400; // consigue el ancho de la primera imagen y lo añade como margen
                // si clickeas el botón de la izquierda, el carrusel se desplaza con el ancho de la imagen, sino se desplaza a la derecha
                carrusel.scrollLeft += icon.id === "left" ? -firstImgWidth : firstImgWidth;
                setTimeout(() => mostaryocultarBotones(), 60); 
            });
        });

        mostaryocultarBotones (); 
    }
});


