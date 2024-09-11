<?php

// Este script devuelve los permisos de usuario de estar la sesión activa. Si no es así, devuelve un error.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/traer.php";

enum err: int
{
    case CUSTOMER = 0;
    case SALES = 1;
    case ADMIN = 2;
    case NO_SESSION = 3;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::CUSTOMER => "El usuario es cliente.",
            self::SALES => "El usuario es vendedor.",
            self::ADMIN => "El usuario es administrador.",
            self::NO_SESSION => "La sesión no está iniciada"
        };
    }
}

// Responde con un mensaje indicando el tipo de rol del usuario en cuestión.
$response = isset($_SESSION['user']) ?
    match (traerRol($_SESSION['user'])) {
        0 => ['error' => err::CUSTOMER, 'errMsg' => err::CUSTOMER->getMsg()],
        1 => ['error' => err::SALES, 'errMsg' => err::SALES->getMsg()],
        2 => ['error' => err::ADMIN, 'errMsg' => err::ADMIN->getMsg()],
        null => ['error' => err::NO_SESSION, 'errMsg' => err::NO_SESSION->getMsg()]
    }      : ['error' => err::NO_SESSION, 'errMsg' => err::NO_SESSION->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();