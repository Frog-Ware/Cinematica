document.addEventListener('DOMContentLoaded', () => {
    const registrar = document.getElementById('seccionRegistrar');
    const iniciar = document.getElementById('seccionIniciar');
    const botonIniciar = document.getElementById('botonIniciar');
    const botonRegistrar = document.getElementById('botonRegistrar');
    const contenedor = document.getElementById('contenedorLogin');
    const abrirLogin = document.getElementById('abrirlogin');
    const cerrar = document.querySelectorAll('.login__cerrar');
    const botonCerrarSesion = document.getElementById('cerrarSesion');
    let loginEstado = false;
    let anchoVentana = window.innerWidth;
    let alturaVentana = window.innerHeight;


    abrirLogin.addEventListener('click', function () {
        if (loginEstado === false) {
            loginEstado = true;
            contenedor.style.display = 'flex';
            const alturaVentana = window.innerHeight;
            const alturaContenedor = contenedor.offsetHeight;
            const alturaCentrada = (alturaVentana - alturaContenedor) / 2;
            setTimeout(function () {
                contenedor.style.top = alturaCentrada + 'px';
            }, 500);
        }
    });

    cerrar.forEach(function (e) {
        e.addEventListener("click", function () {
            cerrarLogin();
        });
    });

    //cambiar entre register y login

    botonIniciar.addEventListener('click', () => {
        if (anchoVentana < 426) {
            iniciar.style.transform = 'scale(0.5)';
            iniciar.style.left = '-57%';
            registrar.style.left = '20%';
        } else if(anchoVentana < 1000){
            iniciar.style.transform = 'scale(0.5)';
            iniciar.style.left = '-40%';
            registrar.style.left = '40%';
        }else{
            registrar.style.left = '51%';
            iniciar.style.left = '-1%';
        }
        setTimeout(function () {
            registrar.style.zIndex = '5';
        }, 200);
        setTimeout(function () {
            if (anchoVentana < 426) {
                iniciar.style.transform = 'scale(1)';
                registrar.style.left = '0';
                iniciar.style.left = '0';
            } else if(anchoVentana < 1000){
                iniciar.style.transform = 'scale(1)';
                registrar.style.left = '10%';
                iniciar.style.left = '10%';
            }else{
                registrar.style.left = '25%';
                iniciar.style.left = '25%';
            }
        }, 600);

    });

    botonRegistrar.addEventListener('click', function () {
        if (anchoVentana < 426) {
            registrar.style.transform = 'scale(0.5)';
            registrar.style.left = '-57%';
            iniciar.style.left = '20%';
        } else if(anchoVentana < 1000){
            registrar.style.transform = 'scale(0.5)';
            registrar.style.left = '-40%';
            iniciar.style.left = '40%';
        }else{
            registrar.style.left = '51%';
            iniciar.style.left = '-1%';
        }
        setTimeout(function () {
            registrar.style.zIndex = '3';
        }, 200);
        setTimeout(function () {
            if (anchoVentana < 426) {
                registrar.style.transform = 'scale(1)';
                iniciar.style.left = '0';
                registrar.style.left = '0';
            } else if(anchoVentana < 1000){
                registrar.style.transform = 'scale(1)';
                iniciar.style.left = '10%';
                registrar.style.left = '10%';
            }else{
                iniciar.style.left = '25%';
                registrar.style.left = '25%';
            }
        }, 600);

    });

    function cerrarLogin() {
        contenedor.style.top = "-100%";
        loginEstado = false;
        registrar.style.zIndex = '0';
        limpiarCampos();
    }
    

    // Verifica el estado del usuario desde localStorage
    let usuario = JSON.parse(localStorage.getItem('usuario'));
    if (usuario) {
        console.log('Usuario logueado:', usuario);
        abrirLogin.style.display = "none";
        botonCerrarSesion.style.display = 'block';

        // Verifica si el usuario es un administrador
        if (usuario.email.endsWith('@cinematicaAdmin.com')) {
            console.log('Usuario con privilegios de administrador');
            esAdmin = true;
            mostarLinkBackoffice()
            
        } else {
            console.log('Usuario normal');
            esAdmin = false;
            mostarLinkBackoffice()
        }
    } else {
        console.log('No hay un usuario logueado.');
    }

    function mostarLinkBackoffice(){
        
        if(esAdmin){
            
                const ul = document.querySelector('#sideBar ul');
                const nuevoLi = document.createElement('li');
                const nuevoEnlace = document.createElement('a');
                const nuevoBoton = document.createElement('button');

                nuevoEnlace.href = 'backoffice.html';
                nuevoBoton.className = 'sideBar_btn btn_link';
                nuevoBoton.textContent = 'BACKOFFICE'; 
                nuevoEnlace.appendChild(nuevoBoton);
                nuevoLi.appendChild(nuevoEnlace);
                ul.appendChild(nuevoLi);
            
        } else {

                const ul = document.querySelector('#sideBar ul');
                const lis = ul.querySelectorAll('li');
    
                if (lis.length > 0) {
                    const ultimoLi = lis[lis.length - 1];
                    ul.removeChild(ultimoLi);
                } 
        }
        
        
    };
    
    // Función para cerrar sesión
    function cerrarSesion() {
        localStorage.removeItem('usuario');
        abrirLogin.style.display = "block";
        botonCerrarSesion.style.display = 'none';
        location.reload(); 
    }

    // Asigna el evento de cerrar sesión al botón correspondiente
    botonCerrarSesion.addEventListener('click', cerrarSesion);

});


