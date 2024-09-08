document.addEventListener('DOMContentLoaded', function() {
const usuario = JSON.parse(localStorage.getItem('usuario'));
const nombreAdministrador = document.getElementById("nombreAdministrador");

nombreAdministrador.innerHTML = `<h1>CINEMÁTICA</h1> <h2>ADMINISTRADOR: ${usuario.nombre} </h2>`
})