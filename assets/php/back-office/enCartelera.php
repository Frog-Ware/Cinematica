<?php

// Este script añade la pelicula deseada a la cartelera.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case EXISTENT = 2;
    case EMPTY = 3;
    case NOT_SET = 4;
    case DATE_ERROR = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::EXISTENT => "La película a añadir ya está en la cartelera.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado.",
            self::DATE_ERROR => "Fecha anterior a la actual."
        };
    }
}

// Guarda las variables recibidas por POST en un array llamado datos.
$campos = ['idProducto', 'fechaInicio', 'cantidadSemanas'];
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x);

// Devuelve el código de error correspondiente.
$error = comprobarError();
$response = ['error' => $error, 'errMsg' => $error->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError()
{
    global $campos, $datos;

    // Devuelve un código de error si la fecha es anterior a la actual.
    if ($datos['fechaInicio'] < date('Y-m-d'))
        return err::DATE_ERROR;

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($campos as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x)
        if (empty($datos[$x]))
            return err::EMPTY;

    // Devuelve un código de error si la película ya esta en cartelera.
    foreach (traerCartelera('idProducto') as $x)
        if ($x['idProducto'] == $datos['idProducto'])
            return err::EXISTENT;

    // Verifica que la película exista e intenta ingresar la película en la cartelera, devolviendo su correspondiente código de error.
    return (traerPelicula($datos['idProducto'], 'idProducto') != null && nuevaEnCartelera($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
}