<?php

// Este script devuelve un array con todos los datos de las películas en cartelera.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "No hay peliculas disponibles."
        };
    }
}

// Devuelve los datos de las películas de no haber errores y un código de error si no hay resultados.
$datos = empty($_POST['campos']) ?
    traerCartelera('*') : traerCartelera($_POST['campos']);
$response = ($datos != null) ?
    ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'cartelera' => $datos] :
    ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();