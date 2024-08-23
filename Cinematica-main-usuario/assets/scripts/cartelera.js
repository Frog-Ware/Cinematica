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
    fetch('assets/php/page/cartelera.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        // Asumiendo que `data.cartelera` es la lista de películas que necesitas procesar
        let cartelera = data.cartelera;
        console.log(cartelera);
        

        if (!cartelera || cartelera.length === 0) {
            console.log('cartelera vacía');
            return;
        }

        const contenedor = document.getElementById('contenedor');
        
        cartelera.forEach(pelicula => {
            const peliculaContenedor = document.createElement('div');
            peliculaContenedor.classList.add('contenedor__pelicula');
            
            //imagen de la cartelera
            const peliculaPoster = document.createElement('div');
            peliculaPoster.classList.add('contenedor__pelicula__poster');
            const contenedorPoster = document.createElement('div');
            const poster = document.createElement('img');
            poster.src = 'assets/img/peliculas/' + pelicula.poster;
            contenedorPoster.appendChild(poster);
            peliculaPoster.appendChild(contenedorPoster);
            
            const peliculaInfo = document.createElement('div');
            peliculaInfo.classList.add('contenedor__pelicula__info');
            
            //nombre de la pelicula 
            const peliculaNombre = document.createElement('h2');
            peliculaNombre.textContent = pelicula.nombrePelicula;
            peliculaInfo.appendChild(peliculaNombre);
            

            //sinopsis
            const peliculaSinopsis = document.createElement('p');
            peliculaSinopsis.textContent = pelicula.sinopsis;
            peliculaInfo.appendChild(peliculaSinopsis);
            
            const peliculaEtiquetas = document.createElement('div');
            peliculaEtiquetas.classList.add('contenedor__pelicula__info--etiquetas');
            
            //generos
            const peliculaGenero = document.createElement('h3');
            peliculaGenero.textContent = pelicula.nombreCategoria;
            peliculaEtiquetas.appendChild(peliculaGenero);
            
            //pegi
            const peliculaPegi = document.createElement('h3');
            peliculaPegi.textContent = pelicula.pegi;
            peliculaEtiquetas.appendChild(peliculaPegi);
            
            //DIMENSION
            const peliculaDimension = document.createElement('h3');
            peliculaDimension.textContent = pelicula.dimension;
            peliculaEtiquetas.appendChild(peliculaDimension);
            
            peliculaInfo.appendChild(peliculaEtiquetas);
            peliculaContenedor.appendChild(peliculaPoster);
            peliculaContenedor.appendChild(peliculaInfo);
            contenedor.appendChild(peliculaContenedor);
        });
    })
    .catch(error => {
        console.error('Error al cargar los datos:', error);
    });
});
