document.addEventListener('DOMContentLoaded', function() {
    try {
        let usuario = JSON.parse(localStorage.getItem('usuario'));

        if (usuario && usuario.email && usuario.email.endsWith('@cinematicaAdmin.com')) {
            console.log('Usuario con privilegios de administrador');
        } else {
            window.location.href = 'index.html';
        }
    } catch (e) {
        console.error('Error al obtener o procesar el usuario:', e);
        window.location.href = 'index.html';
        
    }
});
