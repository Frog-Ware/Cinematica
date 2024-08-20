<?php

// Este script devuelve las listas de los valores desplegables.

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
            self::NO_SUCCESS => "No hay desplegables disponibles."
        };
    }
}

// Asigna los datos extraidos de la base de datos a su correspondiente posición en el array.
$datos['categorias'] = traerCategorias();
$datos['dimensiones'] = traerDimensiones();
$datos['idiomas'] = traerIdiomas();

// Envia los datos mediante JSON.
$response = ($datos != null) ?
    ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'valores' => $datos] :
    ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();