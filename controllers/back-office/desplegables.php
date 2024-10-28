<?php

// Este script devuelve las listas de los valores desplegables.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/traer.php";

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
    isset($_SESSION['user']) && traerRol($_SESSION['user']) != 0 ?
        main() : header('HTTP/1.1 401 Unauthorized', true, 401);
} else {
    header('HTTP/1.0 405 Method Not Allowed', true, 405);
}

// Mata la ejecución.
die();



// Funciones

function main()
{
    // Asigna los datos extraidos de la base de datos a su correspondiente posición en el array.
    $datos['categorias'] = traerCategorias();
    $datos['dimensiones'] = traerDimensiones();
    $datos['idiomas'] = traerIdiomas();

    // Envia los datos mediante JSON.
    $response = (!is_null($datos)) ?
        ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'datos' => $datos] :
        ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];
    echo json_encode($response);
}