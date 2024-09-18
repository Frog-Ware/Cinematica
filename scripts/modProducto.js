const sideBar = document.getElementById("sideBar");
const botonAbrir = document.getElementById("botonAbrir");
const cerrar = document.getElementById("cerrar");
let sideBarState = false;
const contimagen = document.getElementById('cont-img-form');
const inputnom = document.getElementById("input-nombre");
const inputdesc = document.getElementById("input-desc");
const inputprecio = document.getElementById("input-precio");
const contenedorFormulario = document.getElementById("contenedorFormulario");
const imgForm = document.createElement('IMG');
const boton = document.getElementById("boton");
const botondel = document.getElementById("botondel");

contenedorFormulario.appendChild(imgForm);
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
    // const productos = [
    //     {
    //         nombre: 'POP GRANDE',
    //         imagen: 'assets/img/productos/pop_2.webp',
    //         precio: '$145',
            
    //     },
    //     {
    //         nombre: 'COMBO GRANDE',
    //         imagen: 'assets/img/productos/pop_1.webp',
    //         precio: '$380',
           
    //     },
    //     {
    //         nombre: 'COMBO PARA DOS ',
    //         imagen: 'assets/img/productos/pop_3.webp',
    //         precio: '$1175',
            
    //     },
    //     {
    //         nombre: 'BEBIDA',
    //         imagen: 'assets/img/productos/bebida.webp',
    //         precio: '$450',
            
    //     },
    //     {
    //         nombre: 'DORITOS',
    //         imagen: 'assets/img/productos/doritos.webp',
    //         precio: '$450',
            
    //     },
    //     {
    //         nombre: 'PACK 4 MONSTERS',
    //         imagen: 'assets/img/productos/monster.webp',
    //         precio: '$450',
            
    //     },
    // ];

    fetch("assets/php/page/articulos.php", {
        method: "POST",
      })
        .then((response) => response.json())
        .then((data) => {
            let productos = data.articulos;
            console.log(productos);
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
            nombreProducto.textContent = producto.nombreArticulo;
            nombreProducto.classList.add('Producto_nombre');
    
            const productoDivImg = document.createElement('div');
            productoDivImg.classList.add('Producto_img');
            const productoImg = document.createElement('img');
            productoImg.src = 'assets/img/productos/' + producto.imagen;
            productoImg.alt = producto.nombreArticulo;
    
            const productoContador = document.createElement('div');
            productoContador.classList.add('Producto_precio');
    
            const contadorDiv = document.createElement('div');
            const ProductoPrecio = document.createElement('h3');
            ProductoPrecio.textContent = `$ ${producto.precio}`
    
            const botonMas = document.createElement('button');
            botonMas.innerHTML = '&plus;';
    
            const botonMenos = document.createElement('button');
            botonMenos.innerHTML = '&minus;';
    
            contadorDiv.appendChild(ProductoPrecio);
    
            productoDivImg.appendChild(productoImg);
    
            const Producto = document.createElement('div');
            Producto.classList.add('Producto');
            Producto.appendChild(productoDivImg);
            Producto.appendChild(productoContador);
            productoContador.appendChild(contadorDiv)
    
            divProducto.appendChild(nombreProducto);
            divProducto.appendChild(Producto);
            contenedorProductosProductos.appendChild(divProducto);
            
            boton.disabled = true;
            botondel.disabled = true;
            

            let productoExistente = document.querySelector(`#producto_${index}`);
            
            imgForm.classList.add("imagenFormulario")
            divProducto.addEventListener('click', function () {
                imgForm.src = 'assets/img/productos/' + producto.imagen;
                inputnom.value = producto.nombreArticulo;
                inputprecio.value = producto.precio;    
                inputdesc.value = producto.descripcion;
                inputnom.removeAttribute("readOnly");  
                inputprecio.removeAttribute("readOnly");   
                inputdesc.removeAttribute("readOnly");
                boton.disabled = false;
                botondel.disabled = false;
                    // const contimagen = document.getElementById("cont-img-form");
                    // const inputnom = document.getElementById("input-nombre");
                    // const inputdesc = document.getElementById("input-desc");
                    // const inputprecio = document.getElementById("input-precio");
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

    

        });