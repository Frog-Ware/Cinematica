<?php

// Este script devuelve los datos del usuario de estar la sesión activa. Si no es así, devuelve un error.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
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
            self::SUCCESS => "La sesión esta iniciada.",
            self::NO_SUCCESS => "No esta iniciada la sesión."
        };
    }
}

// Si hay una sesión iniciada, guarda los datos del usuario en cuestión como respuesta. Si no es así, guarda un mensaje de error.
$response = (session_status() === PHP_SESSION_ACTIVE) ?
    ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'datosUsuario' => traerUsuario($_SESSION['user'])] :
    ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];

// Envía la respuesta mediante JSON.
echo json_encode($response);

// Mata la ejecución.
die();