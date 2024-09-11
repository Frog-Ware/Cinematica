<?php

// Este script cierra la sesión y envía un código de error.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();

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

// Si hay una sesión iniciada, la cierra. Notifica de su exito.
if (isset($_SESSION['user'])) {
    session_destroy();
    $response = ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg()];
} else {
    $response = ['error' => err::NO_SESSION, 'errMsg' => err::NO_SESSION->getMsg()];
}
echo json_encode($response);

// Mata la ejecución.
die();