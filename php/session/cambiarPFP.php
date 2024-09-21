<?php

// Este script permite cambiar la imagen de perfil asociada a una cuenta en particular.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/traer.php";
require_once "../db/insertar.php";
require_once "../utilities/validacion.php";

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
            self::IMG_ERR => "No se encontró una imagen que coincidiera con el nombre ingresado."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Devuelve el código de error correspondiente por JSON.
    $datos = [];
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
    // Devuelve un código de error si una variable no esta seteada.
    if (isset($_POST['imagenPerfil'])){
        // Guarda el nombre de la imagen elegida por el usuario.
        $img = $_POST['imagenPerfil'];
    } else {
        return err::NOT_SET;
    }

    // Devuelve un código de error si una variable esta vacía.
    if (blank($img))
        return err::EMPTY;

    // Devuelve un código de error si la imagen no existe.
    if (!file_exists("../../assets/img/perfil/$img"))
        return err::IMG_ERR;

    // Devuelve un código de error si la sesión no está iniciada.
    if (isset($_SESSION['user']))
        $email = $_SESSION['user'];
    else
        return err::NO_SESSION;

    // Intenta actualizar la imagen en la base de datos y devuelve su correspondiente código de error.
    return (actImagenPerfil([$img, $email])) ?
        err::SUCCESS : err::NO_SUCCESS;
}