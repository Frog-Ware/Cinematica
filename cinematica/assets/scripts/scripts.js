const sideBar = document.getElementById("sideBar");
const botonAbrir = document.getElementById("botonAbrir");
const cerrar = document.getElementById("cerrar");
let sideBarState = false;

// Función para abrir y cerrar el sidebar
function sideBarFunction() {
    if (sideBarState == false) {
        sideBar.style.left = "-20px";
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


//slider

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

document.addEventListener("DOMContentLoaded", function () {
    const sliderCartelera = document.querySelector(".cartelera_slider");
    const botonIzq = document.querySelector(".cartelera_boton--izq");
    const botonDer = document.querySelector(".cartelera_boton--der");

    const scrollStep = 300; // Ajusta este valor según tus necesidades

    botonIzq.addEventListener("click", () => {
        sliderCartelera.scrollBy({
            left: -scrollStep,
            behavior: 'smooth'
        });
    });

    botonDer.addEventListener("click", () => {
        sliderCartelera.scrollBy({
            left: scrollStep,
            behavior: 'smooth'
        });
    });
});
