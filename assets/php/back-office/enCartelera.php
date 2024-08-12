<?php

// Este script añade la pelicula deseada a la cartelera.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Asigna un código de error según el caso.
enum codigoError: int
{
    case SUCCESS = 0; // Procedimiento realizado con éxito.
    case NO_SUCCESS = 1; // Hubo un error en la inserción en la base de datos.
    case EXISTENT = 2; // La película a añadir ya está en la cartelera.
    case EMPTY = 3; // Al menos un campo está vacio.
    case NOT_SET = 4; // Al menos un campo no está asignado.
    case DATE_ERROR = 5; // Fecha anterior a la actual.
}

// Guarda las variables recibidas por POST en un array llamado datos.
$campos = ['idProducto', 'fechaInicio', 'cantidadSemanas'];
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x, FILTER_SANITIZE_STRING);

// Devuelve el código de error correspondiente.
$response['error'] = comprobarError();
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError()
{
    global $campos, $datos;

    // Devuelve un código de error si la fecha es anterior a la actual.
    if ($datos['fechaInicio'] < date('Y-m-d'))
        return codigoError::DATE_ERROR;

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($campos as $x)
        if (!isset($datos[$x]))
            return codigoError::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x)
        if (empty($datos[$x]))
            return codigoError::EMPTY;

    // Devuelve un código de error si la película ya esta en cartelera.
    foreach (traerCartelera() as $x)
        if ($x['idProducto'] == $datos['idProducto'])
            return codigoError::EXISTENT;

    // Verifica que la película exista e intenta ingresar la película en la cartelera, devolviendo su correspondiente código de error.
    return (traerPelicula($datos['idProducto']) != null && nuevaEnCartelera($datos)) ?
        codigoError::SUCCESS : codigoError::NO_SUCCESS;
}