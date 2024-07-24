<?php

// Este script registra una nueva película o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json");
require_once "../db/insertar.php";
require_once "../db/traer.php";

// Asigna un código de error según el caso.
enum codigoError: int{
    case SUCCESS = 0; // Procedimiento realizado con éxito.
    case NO_SUCCESS = 1; // Hubo un error en la inserción en la base de datos.
    case EMPTY = 2; // Al menos un campo está vacio.
    case NOT_SET = 3; // Al menos un campo no está asignado.
}

// Guarda las variables sanitizadas en un array llamado datos y los valores multiples en otro array llamado valores.
$campos = ['nombreArticulo', 'descripcion', 'precio', 'imagen'];
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x, FILTER_SANITIZE_STRING);

// Genera una ID para el producto.
$datos['idProducto'] = generarID();

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

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x)
        if (empty($_POST[$x])) return codigoError::EMPTY;

    // Intenta ingresar el artículo en la base de datos y devuelve su correspondiente código de error.
    return (nuevoArticulo($datos)) ?
        codigoError::SUCCESS : codigoError::NO_SUCCESS;
}

// Genera un ID de 11 numeros aleatorios.
function generarID() {
    do $id = mt_rand(1000000000, 9999999999);
        while (!verificarExistente($id));
    return $id;
}

// Verifica que el id no este ya asociado a un producto.
function verificarExistente($id) {
    foreach (traerArticulos() as $x)
        if ($x['idProducto'] == $id) return false;
    return true;
}