<?php

// Este script permite cambiar la imagen de perfil asociada a una cuenta en particular.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/traer.php";
require_once "../db/insertar.php";
require_once "../config/acceso.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case NO_SESSION = 2;
    case EMPTY = 3;
    case NOT_SET = 4;
    case IMG_ERR = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un problema al insertarlo en la base de datos.",
            self::NO_SESSION => "La sesión no está iniciada.",
            self::EMPTY => "El campo está vacio.",
            self::NOT_SET => "El campo no está asignado.",
            self::IMG_ERR => "No existe una imagen con ese nombre."
        };
    }
}

// Sanitiza el dato ingresado.
$datos['imagenPerfil'] = filter_input(INPUT_POST, 'imagenPerfil');

// Devuelve el código de error correspondiente.
$error = comprobarError();
$response = ['error' => $error, 'errMsg' => $error->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError()
{
    global $datos;

    // Devuelve un código de error si una variable no esta seteada.
    if (!isset($datos['imagenPerfil']))
        return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    if (empty($datos['imagenPerfil']))
        return err::EMPTY;

    // Devuelve un código de error si la imagen no existe.
    if (!file_exists("../../img/perfil/" . $datos['imagenPerfil']))
        return err::IMG_ERR;

    // Devuelve un código de error si la sesión no está iniciada.
    if (isset($_SESSION['user']))
        $datos['email'] = $_SESSION['user'];
    else
        return err::NO_SESSION;

    // Intenta actualizar la imagen en la base de datos y devuelve su correspondiente código de error.
    return (actImagen($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
}