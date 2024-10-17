<?php

// Este script evuelve el carrito asociado con el usuario.

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
            self::NO_SUCCESS => "No hay datos disponibles."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Devuelve los valores del carrito y un mensaje de error por JSON.
    $datos = isset($_SESSION['user']) ?
        traerCarrito($_SESSION['user']) : null;
    $response = (!is_null($datos)) ?
        ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'carrito' => $datos] :
        ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();