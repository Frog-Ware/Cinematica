//Variable utilizada para traer los datos del usuario
let usuario = JSON.parse(localStorage.getItem("usuario"))
//Variables utlizadas para los divs del perfil
let tarjetaUsuario = document.getElementById("perfilDeUsuario")
let loginBarState = false

let tarjetaCambioFotos = document.getElementById("perfilCambioFoto")
let botonAtras = document.getElementById("botonAtrasPerfil")
let loginFotoBarState = false

let botonAjustes = document.getElementById("botonCambiosCuenta")

let imagenCortada = null

//Variables utilizadas para la foto de perfil del usuario
let fotosGrid = document.querySelectorAll(".agrandar_imagen")
let fotoPerfil = document.getElementById("imagenDePerfil")
let fotoNuevaPreview = document.getElementById("imagenDePerfilCambio")

const botonAbrirLogin = document.getElementById("abrirLoginSide")




//Para abrir y cerrar el perfil de usuario
function loginBarFunction() {
  if (loginBarState == false) {
    tarjetaUsuario.style.marginLeft = "69.9%";
    loginBarState = true;
  } else {
    tarjetaUsuario.style.marginLeft = "130%";
    loginBarState = false;
  }
}
botonAbrirLogin.onclick = (event) => {
  event.stopPropagation();
  if (loginFotoBarState == true) {
    tarjetaCambioFotos.style.marginLeft = "130%"
    loginFotoBarState = false
    loginBarFunction();
  }else{
    loginBarFunction();
  }

}
//Cambiar entre div perfil y div cambio de imagen
fotoPerfil.addEventListener("click", function () {
  tarjetaUsuario.style.marginLeft = "130%"
  loginBarState = false
  tarjetaCambioFotos.style.marginLeft = "69.9%"
  loginFotoBarState = true
})

  botonAtras.addEventListener("click", function () {
      tarjetaCambioFotos.style.marginLeft = "130%"
      loginFotoBarState = false
      tarjetaUsuario.style.marginLeft = "69.9%"
      loginBarState = true
  })


// Boton de "cuenta" en el div de perfil que te redirije al html de cambiar contraseña/datos
botonAjustes.addEventListener("click", function(){
  window.location.href = "changepasswd.html"
})

//Funciones para agrandar y achicar el icono de perfil
function agrandarImg(x) {
  x.style.width = "200px"
  x.style.height = "200px"

}
function imgNormal(x) {
  x.style.width = "175px"
  x.style.height = "175px"
}


// If que pone en el div de "perfil" los datos del usuario
if (usuario) {
  botonAbrirLogin.style.display = "block"
  const dataDiv = document.getElementById("divData")
  let nombreCompleto = usuario.nombre + " " + usuario.apellido;
  dataDiv.innerHTML = `<h2> ${nombreCompleto} </h2>
                       <h2>  ${usuario.email} </h2>`
  fotoPerfil.src = "assets/img/perfil/" + usuario.imagenPerfil;
  fotoNuevaPreview.src = "assets/img/perfil/" + usuario.imagenPerfil;
}

//Funcion que cambia la imagen del div en el que esta la galeria de imagenes para previsualizar
function cambiarImagenPerfil(imagen) {
  let imgPerfil = document.getElementById("imagenDePerfilCambio");
  imgPerfil.src = imagen.src;
  let imagenParaIndex = imgPerfil.src
  let indiceImagen = imagenParaIndex.lastIndexOf("/") + 1
  imagenCortada = imagenParaIndex.slice(indiceImagen)
}


//Evento onclick en el boton actualizar perfil
document.getElementById('actualizarFoto').addEventListener('click', function() {
      // Obtener la ruta de la imagen seleccionada
      let imagenFullActualizada = imagenCortada
    const email = usuario.email
    const datos = new FormData()
    datos.append('email', email);
    datos.append('imagenPerfil', imagenFullActualizada);
    fetch('assets/php/session/cambiarPFP.php', {
      method: 'POST',
      body: datos
    })
    .then(response => {
      return response.text().then(texto => {
          try {
              return JSON.parse(texto)
          } catch (error) {
              console.error('Error al analizar JSON:', error.message)
              throw new Error('La respuesta no es un JSON válido: ' + texto)
          }
      })
  })
  .then(data => {
    usuario.imagenPerfil = imagenFullActualizada;
    localStorage.setItem("usuario", JSON.stringify(usuario));
    document.getElementById('imagenDePerfil').src = `assets/img/perfil/${usuario.imagenPerfil}`;
  })
  .catch(error => {
      console.error('Error:', error);
      alert('Ocurrió un error al enviar los datos');
  })
})
// window.onload = function() {
//   const imagenes = [
//       { 
//         src: "assets/img/perfil/2.webp", 
//         id: "img1" 
//       },
//       { 
//         src: "assets/img/perfil/2a.jpg", 
//         id: "img2" 
//       },
//       { 
//         src: "assets/img/perfil/images.jpg", 
//         id: "img3" 
//       },
//       { 
//         src: "assets/img/perfil/Jermzlee-carlino-simpatico.jpg", 
//         id: "img4" 
//       },
//       { 
//         src: "assets/img/perfil/perfiles-de-instagram-sobre-perros.jpg", 
//         id: "img5" 
//       },
//       { 
//         src: "assets/img/perfil/pexels-pixabay-39317-1024x683.jpg",
//         id: "img6" 
//       }
//   ];



