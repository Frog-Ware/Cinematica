<?php

// Este script devuelve las listas de los valores desplegables.

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
            self::NO_SUCCESS => "No hay desplegables disponibles."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Asigna los datos extraidos de la base de datos a su correspondiente posición en el array.
    $datos['nombreCategoria'] = array_column(traerCategorias(), 'nombreCategoria');
    $datos['dimension'] = array_column(traerDimensiones(), 'dimension');
    $datos['idioma'] = array_column(traerIdiomas(), 'idioma');

    // Envia los datos mediante JSON.
    $response = ($datos != null) ?
        ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'valores' => $datos] :
        ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];
    echo json_encode($response);
} else {
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();