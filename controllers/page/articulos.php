<?php

// Este script devuelve un array con todos los datos de los artículos.

header("Content-Type: application/json; charset=utf-8");
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";

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

// Verifica el método utilizado y envia un error 405 de no ser el permitido.
$_SERVER['REQUEST_METHOD'] == 'POST' ?
    main() : header('HTTP/1.0 405 Method Not Allowed', true, 405);

// Mata la ejecución.
die();



// Funciones

function main()
{
    // Devuelve los datos de los artículos si no hay errores y un código de error si no hay resultados.
    $datos = traerArticulos();
    $response = ($datos != null) ?
        ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'datos' => $datos] :
        ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];
    echo json_encode($response);
}