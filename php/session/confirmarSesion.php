<?php

// Este script devuelve los datos del usuario de estar la sesión activa. Si no es así, devuelve un error.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/traer.php";

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

// Si hay una sesión iniciada, envía los datos del usuario en cuestión como respuesta, además de sus respectivos mensajes de error.
$response = isset($_SESSION['user']) ?
    ['error' => err::EXISTS, 'errMsg' => err::EXISTS->getMsg(), 'datosUsuario' => traerUsuario($_SESSION['user'])] :
    ['error' => err::NO_SESSION, 'errMsg' => err::NO_SESSION->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();