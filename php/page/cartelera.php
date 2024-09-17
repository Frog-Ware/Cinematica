<?php

// Este script devuelve un array con todos los datos de las películas en cartelera.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";
require_once "../utilities/validacion.php";

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

// Devuelve los datos de las películas si no hay errores y un código de error si no hay resultados.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = traerCartelera();
    $response = ($datos != null) ?
        ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'datos' => $datos] :
        ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();