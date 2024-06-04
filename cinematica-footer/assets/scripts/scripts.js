const sideBar = document.getElementById("sideBar");
const botonAbrir = document.getElementById("botonAbrir");
const cerrar = document.getElementById("cerrar");
let sideBarState = false;

// Función para abrir y cerrar el sidebar
function sideBarFunction() {
    if (sideBarState == false) {
        sideBar.style.left = "-0px";
        sideBarState = true;
    } else {
        sideBar.style.left = "-270px";
        sideBarState = false;
    }
}

// Asignar la función sideBarFunction al evento onclick del icono de apertura y al icono de cierre
botonAbrir.onclick = (event) => {
    event.stopPropagation(); // Detener la propagación del evento para evitar que cierre el sidebar inmediatamente
    sideBarFunction();
};

cerrar.onclick = () => sideBarFunction();

// Cerrar el sidebar al hacer clic fuera de él
document.onclick = function (e) {
    if (e.target.id !== "sideBar" && e.target.id !== "botonAbrir") {
        sideBar.style.left = "-270px";
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
});


//slider cartelera

document.addEventListener('DOMContentLoaded', function() {
    const movies = [
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        },
        {
            image: 'assets/img/bojack.jpg'
        }
    ];

    const contenedorPeliculas = document.getElementById('peliculas');

    movies.forEach(movie => {
        const pelicula = document.createElement('img');
        pelicula.src = movie.image;
        contenedorPeliculas.appendChild(pelicula);
    });


    const carousel = document.querySelector(".carrusel"),
firstImg = carousel.querySelectorAll("img")[0],
arrowIcons = document.querySelectorAll(".cartelera i");


const showHideIcons = () => {
    // muestra o esconde el boton al llegar a un extremo
    let scrollWidth = carousel.scrollWidth - carousel.clientWidth; // muestra el maximo scrolleo
    arrowIcons[0].style.display = carousel.scrollLeft == 0 ? "none" : "block";
    arrowIcons[1].style.display = carousel.scrollLeft == scrollWidth ? "none" : "block";
}

arrowIcons.forEach(icon => {
    icon.addEventListener("click", () => {
        let firstImgWidth = firstImg.clientWidth + 400; // consigue el ancho de la primera imagen y lo añade como margen
        // si clickeas el boton de la izquierda, .el carrusel se desplaza con el ancho de la imagen sino se desplaza a la derecha
        carousel.scrollLeft += icon.id == "left" ? -firstImgWidth : firstImgWidth;
        setTimeout(() => showHideIcons(), 60); // oculta el botone despues de 60ms
    });
});

});


