<?php

// Este script registra una nueva película o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header ("Content-Type: application/json; charset=utf-8");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../files/subir.php";

// Asigna un código de error según el caso.
enum codigoError: int{
    case SUCCESS = 0; // Procedimiento realizado con éxito.
    case NO_SUCCESS = 1; // Hubo un error en la inserción en la base de datos.
    case EXISTENT = 2; // La película a añadir ya está en la base de datos.
    case EMPTY = 3; // Al menos un campo está vacio.
    case NOT_SET = 4; // Al menos un campo no está asignado.
    case IMG_ERROR = 5; // Al menos una imagen tiene un error.
}

// Genera una ID para el producto.
$datos['idProducto'] = generarID();

// Guarda las variables sanitizadas en un array llamado datos y los valores multiples en otro array llamado valores.
$campos = ['nombreArticulo', 'descripcion', 'precio'];
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x, FILTER_SANITIZE_STRING);

// Devuelve el código de error correspondiente.
$response['error'] = comprobarError();
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError() {
    global $campos, $datos;

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($campos as $x)
        if (!isset($_POST[$x])) return codigoError::NOT_SET;
    if (!isset($_FILES['imagen'])) return codigoError::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x)
        if (empty($_POST[$x])) return codigoError::EMPTY;
    if (empty($_FILES['imagen'])) return codigoError::EMPTY;

    // Devuelve un código de error si hay una película ingresada con el mismo nombre y director.
    $comp = traerArticulos();
    foreach ($comp as $x)
        if ($x['nombreArticulo'] == $datos['nombreArticulo']) return codigoError::EXISTENT;

    // Guarda el nombre de la imagen en datos.
    $ext = ($_FILES['imagen']['type'] == "image/jpeg") ?
        '.jpg' : '.png';
    $datos['imagen'] = str_replace(" ", "_", $datos['nombreArticulo'] . $ext);

    // Intenta subir las imagenes a la carpeta.
    if (!subirImg($_FILES['imagen'], $datos['imagen'], 'productos')) return codigoError::IMG_ERROR;

    // Intenta ingresar el artículo en la base de datos y devuelve su correspondiente código de error.
    return (nuevoArticulo($datos)) ?
        codigoError::SUCCESS : codigoError::NO_SUCCESS;
}

// Genera un ID de 11 numeros aleatorios.
function generarID() {
    do $id = mt_rand(100000000, 999999999);
        while (!in_array($id, array_column(traerArticulos(), 'idProducto')));
    return $id;
}