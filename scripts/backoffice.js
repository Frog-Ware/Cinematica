const sideBar = document.getElementById("sideBar");
const botonAbrir = document.getElementById("botonAbrir");
const cerrar = document.getElementById("cerrar");
let sideBarState = false;
const nombreAdmin = document.getElementById("nombreAdmin");

let usuario = JSON.parse(localStorage.getItem('usuario'));
nombreAdmin.innerHTML = usuario.nombre;


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
    event.stopPropagation(); 
    sideBarFunction();
};

cerrar.onclick = () => sideBarFunction();

document.onclick = function (e) {
    if (e.target.id !== "sideBar" && e.target.id !== "botonAbrir") {
        sideBar.style.left = "-281px";
        sideBarState = false;
    }
};

document.getElementById('enviar').addEventListener('click', function() {
    const sinopsis = document.getElementById('sinopsis').value;
    const nombre = document.getElementById('nombre').value;
    const actores = document.getElementById('actores').value;
    const duracion = document.getElementById('Duracion').value;
    const trailer = document.getElementById('Trailer').value;
    const pegi = document.getElementById('pegi').value;
    const director = document.getElementById('director').value;
    const categoria = document.getElementById('categoria').value;
    const categoria1 = document.getElementById('categoria1').value;
    const idioma = document.getElementById('idiomas').value;
    const idioma1 = document.getElementById('idiomas1').value;
    const dimension = document.getElementById('dimension').value;
    const dimension1 = document.getElementById('dimension1').value;
    const nombreAdmin = document.getElementById('nombreAdmin')

    let usuario = JSON.parse(localStorage.getItem('usuario'));
    nombreAdmin.innerHTML = usuario.nombre;

    let categorias;
    if (categoria && categoria1) {
        categorias = categoria + ", " + categoria1;
    } else {
        categorias = categoria || categoria1;
    }

    let idiomas;
    if (idioma && idioma1) {
        idiomas = idioma + ", " + idioma1;
    } else {
        idiomas = idioma || idioma1;
    }

    let dimensiones;
    if (dimension && dimension1) {
        dimensiones = dimension + ", " + dimension1;
    } else {
        dimensiones = dimension || dimension1;
    }

    const formData = new FormData();

    const posterInput = document.getElementById('Poster');
    const posterFile = posterInput.files[0];
    if (posterFile) {
        formData.append('poster', posterFile);
    }

    const cabeceraInput = document.getElementById('Cabecera');
    const cabeceraFile = cabeceraInput.files[0];
    if (cabeceraFile) {
        formData.append('cabecera', cabeceraFile);
    }

    formData.append('sinopsis', sinopsis);
    formData.append('nombrePelicula', nombre);
    formData.append('actores', actores);
    formData.append('duracion', duracion);
    formData.append('trailer', trailer);
    formData.append('pegi', pegi);
    formData.append('director', director);
    formData.append('categorias', categorias);
    formData.append('idiomas', idiomas);
    formData.append('dimensiones', dimensiones);

    fetch('assets/php/back-office/nuevaPelicula.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        return response.text().then(texto => {
            try {
                return JSON.parse(texto);
            } catch (error) {
                console.error('Error al analizar JSON:', error.message);
                throw new Error('La respuesta no es un JSON válido: ' + texto);
            }
        });
    })
    .then(data => {
        console.log('Success:', data);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al enviar los datos');
    });

    limpiarCampos();
    
});

    function previewImage(event, previewId) {
        const preview = document.getElementById(previewId);
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.innerHTML = '';
            preview.appendChild(img);
        };

        reader.readAsDataURL(file);
    }

function limpiarCampos() {
    document.getElementById('Poster').value = '';
    document.getElementById('Cabecera').value = '';

    document.getElementById('sinopsis').value = '';
    document.getElementById('nombre').value = '';
    document.getElementById('actores').value = '';
    document.getElementById('Duracion').value = '';
    document.getElementById('Trailer').value = '';
    document.getElementById('director').value = '';

    document.getElementById('pegi').selectedIndex = 0;
    document.getElementById('categoria').selectedIndex = 0;
    document.getElementById('categoria1').selectedIndex = 0;
    document.getElementById('idiomas').selectedIndex = 0;
    document.getElementById('idiomas1').selectedIndex = 0;
    document.getElementById('dimension').selectedIndex = 0;
    document.getElementById('dimension1').selectedIndex = 0;


    document.getElementById('poster-preview').innerHTML = '<p>Previsualización del póster</p>';
    document.getElementById('cabecera-preview').innerHTML = '<p>Previsualización de la cabecera</p>';
}
