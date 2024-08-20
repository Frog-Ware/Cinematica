<?php

// Este script cierra la sesión y envía un código de error.

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
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error."
        };
    }
}

print(session_status());
// Si hay una sesión iniciada, la cierra.
if (isset($_SESSION['user'])) session_destroy();
print_r($_SESSION);

// Envía la respuesta mediante JSON.


// Mata la ejecución.
die();