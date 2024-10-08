<?php

// Este script devuelve una lista con los administradores.

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
            self::NO_SUCCESS => "Hubo un error o la lista de clientes esta vacía."
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
    // Asigna los datos extraidos de la base de datos a una variable llamada datos.
    $datos = traerEmpleados(1);

    // Envia los datos mediante JSON.
    $response = (!is_null($datos)) ?
        ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'datos' => $datos] :
        ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];
    echo json_encode($response);
}