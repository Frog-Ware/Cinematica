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


botonAbrir.onclick = (event) => {
    event.stopPropagation(); // Detiene la propagación del evento para evitar que cierre el sidebar inmediatamente
    sideBarFunction();
};

cerrar.onclick = () => sideBarFunction();

// Cierra el sidebar al hacer clic fuera de el
document.onclick = function (e) {
    if (e.target.id !== "sideBar" && e.target.id !== "botonAbrir") {
        sideBar.style.left = "-281px";
        sideBarState = false;
    }
};

document.addEventListener('DOMContentLoaded', function () {
    const productos = [
        {
            nombre: 'POP GRANDE',
            imagen: 'assets/img/productos/pop_2.webp',
            precio: '$145',
            
        },
        {
            nombre: 'COMBO GRANDE',
            imagen: 'assets/img/productos/pop_1.webp',
            precio: '$380',
           
        },
        {
            nombre: 'COMBO PARA DOS ',
            imagen: 'assets/img/productos/pop_3.webp',
            precio: '$1175',
            
        },
        {
            nombre: 'BEBIDA',
            imagen: 'assets/img/productos/bebida.webp',
            precio: '$450',
            
        },
        {
            nombre: 'DORITOS',
            imagen: 'assets/img/productos/doritos.webp',
            precio: '$450',
            
        },
        {
            nombre: 'PACK 4 MONSTERS',
            imagen: 'assets/img/productos/monster.webp',
            precio: '$450',
            
        },
    ];
    const contenedorProductos = document.getElementById("contenedor");
    const contenedorProductosProductos = document.createElement('div');
    contenedorProductosProductos.classList.add('contenedorProductos__productos');
    const contenedorProductosLista = document.createElement('div');
    contenedorProductosLista.classList.add('contenedorProductos__lista');
    let anchoVentana = window.innerWidth;
    let alturaVentana = window.innerHeight;

    productos.forEach((producto, index) => {
        const divProducto = document.createElement('div');

        const nombreProducto = document.createElement('h2');
        nombreProducto.textContent = producto.nombre;
        nombreProducto.classList.add('Producto_nombre');

        const productoDivImg = document.createElement('div');
        productoDivImg.classList.add('Producto_img');
        const productoImg = document.createElement('img');
        productoImg.src = producto.imagen;
        productoImg.alt = producto.nombre;

        const productoContador = document.createElement('div');
        productoContador.classList.add('Producto_precio');

        const contadorDiv = document.createElement('div');
        const ProductoPrecio = document.createElement('h3');
        ProductoPrecio.textContent = producto.precio;

        const botonMas = document.createElement('button');
        botonMas.innerHTML = '&plus;';

        const cont = document.createElement('input');
        cont.type = 'text';
        cont.readOnly = true;
        cont.value = 0;
        cont.min = 0;
        cont.max = 20;
        cont.classList.add('contador');

        const botonMenos = document.createElement('button');
        botonMenos.innerHTML = '&minus;';

        contadorDiv.appendChild(ProductoPrecio);
        contadorDiv.appendChild(botonMenos);
        contadorDiv.appendChild(cont);
        contadorDiv.appendChild(botonMas);

        productoDivImg.appendChild(productoImg);

        const Producto = document.createElement('div');
        Producto.classList.add('Producto');
        Producto.appendChild(productoDivImg);
        Producto.appendChild(productoContador);
        productoContador.appendChild(contadorDiv)

        divProducto.appendChild(nombreProducto);
        divProducto.appendChild(Producto);
        contenedorProductosProductos.appendChild(divProducto);



        botonMas.addEventListener('click', function () {
            let valorActual = parseInt(cont.value);
            if (valorActual < cont.max) {
                cont.value = valorActual + 1;
            }
            let productoExistente = document.querySelector(`#producto_${index}`);
            if (!productoExistente) {
                //Se crea el item en el div de la lista de items
                const listaItem = document.createElement('div');
                listaItem.classList.add('lista_item');
                listaItem.id = `producto_${index}`;

                const listaImg = document.createElement('div');
                listaImg.classList.add('lista_img');

                const listaImgImg = document.createElement('img');
                listaImgImg.src = producto.imagen;
                listaImgImg.alt = producto.nombre;
                listaImg.appendChild(listaImgImg);

                const listaContenido = document.createElement('div');
                listaContenido.classList.add('lista_contenido');
                const listaContenidoNombre = document.createElement('h4');
                listaContenidoNombre.textContent = producto.nombre;
                const listaContenidoPrecio = document.createElement('h2');
                listaContenidoPrecio.textContent = producto.precio;
                const listaContenidoCantidad = document.createElement('h3');
                listaContenidoCantidad.textContent = `Cantidad: ${cont.value}`;

                listaContenido.appendChild(listaContenidoNombre);
                listaContenido.appendChild(listaContenidoPrecio);
                listaContenido.appendChild(listaContenidoCantidad);
                listaItem.appendChild(listaImg);
                listaItem.appendChild(listaContenido);
                contenedorProductosLista.appendChild(listaItem);

                contenedorProductos.appendChild(contenedorProductosLista);
                
            }else {
                // Aumenta el valor de cantidad
                let cantidad = productoExistente.querySelector('.lista_contenido h3');
                cantidad.textContent = `Cantidad: ${cont.value}`;
                // Aumenta el Valor del precio
                let precioAumentado = productoExistente.querySelector('.lista_contenido h2');
                let precioNumero = parseInt(producto.precio.replace('$', '')); // Convertir precio a número
                precioAumentado.textContent = `$${precioNumero * parseInt(cont.value)}`; // Actualizar el precio total
                 
            }
        });


        botonMenos.addEventListener('click', function () {
            let valorActual = parseInt(cont.value);
            if (valorActual > 0) {
                cont.value = valorActual - 1;
            }
            let productoExistente = document.querySelector(`#producto_${index}`);
            if (productoExistente) {

                let cantidad = productoExistente.querySelector('.lista_contenido h3');
                cantidad.textContent = `Cantidad: ${cont.value}`;

                if(cont.value == 0){
                    productoExistente.remove();
                } else{
                    let precioAumentado = productoExistente.querySelector('.lista_contenido h2');
                    let precioNumero = parseInt(producto.precio.replace('$', '')); 
                    precioAumentado.textContent = `$${precioNumero * parseInt(cont.value)}`; 
                }
            }
            
        });

    });
    contenedorProductos.appendChild(contenedorProductosProductos);
    window.addEventListener('resize', function () {
        if (window.innerWidth < 769) { // Cambia el valor a la resolución deseada
            contenedorProductosLista.style.display = 'none';
        } else {
            contenedorProductosLista.style.display = 'block';
        }
    });
});



