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
        sideBar.style.left = "-281px";
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
        sideBar.style.left = "-281px";
        sideBarState = false;
    }
};


document.addEventListener('DOMContentLoaded', function() {
    const movies = [
        {
            title: 'Película 1',
            description: 'Descripción de la película 1.',
            image: 'assets/img/bojackCart.jpg'
        },
        {
            title: 'Película 2',
            description: 'Descripción de la película 2.',
            image: 'assets/img/bojackCart.jpg'
        },
        {
            title: 'Película 3',
            description: 'Descripción de la película 3.',
            image: 'assets/img/bojackCart.jpg'
        },
        {
            title: 'Película 3',
            description: 'Descripción de la película 3.',
            image: 'assets/img/bojackCart.jpg'
        },
        {
            title: 'Película 3',
            description: 'Descripción de la película 3.',
            image: 'assets/img/bojackCart.jpg'
        }
        ,
        {
            title: 'Película 3',
            description: 'Descripción de la película 3.',
            image: 'assets/img/bojackCart.jpg'
        }
        ,
        {
            title: 'Película 3',
            description: 'Descripción de la película 3.',
            image: 'assets/img/bojackCart.jpg'
        }
    ];

    const container = document.getElementById('contenedor_peliculas');

    movies.forEach(movie => {
        const movieElement = document.createElement('div');
        movieElement.classList.add('pelicula');

        const movieImage = document.createElement('img');
        movieImage.src = movie.image;
        movieElement.appendChild(movieImage);

        const movieTitle = document.createElement('h2');
        movieTitle.textContent = movie.title;
        movieElement.appendChild(movieTitle);

        const movieDescription = document.createElement('p');
        movieDescription.textContent = movie.description;
        movieElement.appendChild(movieDescription);

        container.appendChild(movieElement);
    });
});