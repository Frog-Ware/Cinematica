<?php

// Este script cierra la sesión y envía un código de error.

ob_start();
header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SESSION = 1;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Se cerró la sesión con éxito.",
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
    // Si hay una sesión iniciada, la cierra. Devuelve el código de error correspondiente por JSON.
    if (isset($_SESSION['user'])) {
        session_destroy();
        $response = ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg()];
    } else {
        $response = ['error' => err::NO_SESSION, 'errMsg' => err::NO_SESSION->getMsg()];
    }
    
    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}