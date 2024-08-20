<?php

// Este script devuelve un array con todos los datos de las películas que coincidan con lo buscado.

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
            self::NO_SUCCESS => "No hay coincidencia."
        };
    }
}

// Asigna el valor de búsqueda a una variable.
$var = "%" . filter_input(INPUT_POST, 'busqueda') . "%";

// Trae las peliculas coincidentes, de lo contrario devuelve un error.
$datos = empty($_POST['campos']) ?
    traerBusqueda($var, '*') : traerBusqueda($var, $_POST['campos']);
$response = ($datos != null) ?
    ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'resultado' => $datos] :
    ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();