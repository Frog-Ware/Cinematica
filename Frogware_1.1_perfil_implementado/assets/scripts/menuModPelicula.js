document.addEventListener("DOMContentLoaded", () => {
    //Cartelera
    //este fetch trae lso datos de la cartelera, luego con estos datos crea las peliculas e inicia el carrusel
    fetch("assets/php/page/cartelera.php")
        .then((response) => response.json())
        .then((data) => {
            let peliculas = data.cartelera;
            console.log(data.cartelera);

            if (!peliculas || peliculas.length === 0) {
                console.log("cartelera vacía");
                return;
            }

            const contenedorPeliculas = document.getElementById("gridPeli");
            peliculas.forEach((item) => {
                const pelicula = document.createElement("div");
                pelicula.classList.add('grid_item');
                const imagenPeli = document.createElement("img");
                imagenPeli.src = "assets/img/peliculas/" + item.poster;
                pelicula.appendChild(imagenPeli);
                const titulos = document.createElement("h3")
                titulos.textContent = item.nombrePelicula
                titulos.classList.add("titulo")
                pelicula.appendChild(titulos)
                contenedorPeliculas.appendChild(pelicula);

                pelicula.addEventListener("click", () => {
                    localStorage.setItem("peliculas", JSON.stringify(item));
                    window.location.href = "peliculas.html";
                })
            });
        })
});