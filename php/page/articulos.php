<?php

// Este script devuelve un array con todos los datos de los artículos.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";

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
            self::NO_SUCCESS => "No hay artículos disponibles."
        };
    }
}

// Revisa si hay alguna especificación sobre los campos requeridos y trae los datos necesarios.
$datos = empty($_POST['campos']) ?
    traerArticulos('*') : traerArticulos($_POST['campos']);

// Devuelve los datos de los artículos si no hay errores y un código de error si no hay resultados.
$response = ($datos != null) ?
    ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'articulos' => $datos] :
    ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();