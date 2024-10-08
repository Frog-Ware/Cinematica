<?php

// Este script elimina el carrito asociado con el email enviado.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/traer.php";
require_once "../db/insertar.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case NONEXISTENT = 2;
    case NO_SESSION = 3;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la remoción en la base de datos.",
            self::NONEXISTENT => "El carrito no existe.",
            self::NO_SESSION => "La sesión no está iniciada."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar();
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}


// Mata la ejecución.
die();



// Funciones

function comprobar()
{
    // Devuelve un código de error si la sesión no está iniciada.
    if (isset($_SESSION['user'])) {
        $email = $_SESSION['user'];
    } else {
        return err::NO_SESSION;
    }

    // Devuelve un código de error si no existe el artículo a eliminar.
    $carritoDB = traerCarrito($email);
    if (is_null($carritoDB))
        return err::NONEXISTENT;

    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (eliminarAsientos(array_intersect_key($carritoDB, array_flip(['idFuncion', 'asientos'])))) && eliminarCarrito($email) ?
        err::SUCCESS : err::NO_SUCCESS;
}