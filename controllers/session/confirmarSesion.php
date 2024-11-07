<?php

// Este script devuelve los datos del usuario de estar la sesión activa. Si no es así, devuelve un error.

ob_start();
header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case EXISTS = 0;
    case NO_SESSION = 1;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::EXISTS => "La sesión esta iniciada.",
            self::NO_SESSION => "No esta iniciada la sesión."
        };
    }
}

// Restringe el acceso si no se utiliza el método de solicitud adecuado.
$_SERVER['REQUEST_METHOD'] == 'POST' ?
    main() : header('HTTP/1.0 405 Method Not Allowed');

exit;



// Funciones

function main()
{
    // Si hay una sesión iniciada, envía los datos del usuario en cuestión como respuesta. 
    $response = isset($_SESSION['user']) ?
        ['error' => err::EXISTS, 'errMsg' => err::EXISTS->getMsg(), 'datos' => traerUsuario($_SESSION['user'])] :
        ['error' => err::NO_SESSION, 'errMsg' => err::NO_SESSION->getMsg()];
    
    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}