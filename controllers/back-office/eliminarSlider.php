<?php

// Este script permite eliminar una imagen del slider/expositor de la base de datos y el servidor.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/insertar.php";
require_once "../../models/db/traer.php";
require_once "../../models/files/subir.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case NONEXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la remoción en la base de datos.",
            self::NONEXISTENT => "La imagen a eliminar no está en la BD o el servidor.",
            self::VALIDATION => "El nombre no pasó la prueba de validación.",
            self::EMPTY => "El nombre está vacío.",
            self::NOT_SET => "El nombre no está seteado."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    isset($_SESSION['user']) && traerRol($_SESSION['user']) != 0 ?
        main() : header('HTTP/1.1 401 Unauthorized', true, 401);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed', true, 405);
}

// Mata la ejecución.
die();



// Funciones

function main()
{
    // Devuelve el código de error correspondiente.
    $error = comprobar();
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
}

function comprobar()
{
    // Devuelve un código de error si el ID no esta seteado.
    if (isset($_POST['nombre'])) {
        $nmb = $_POST['nombre'];
    } else {
        return err::NOT_SET;
    }

    // Devuelve un código de error si el ID esta vacío.
    if (blank($nmb))
        return err::EMPTY;

    // Devuelve un código de error si el ID no pasa la validación.
    if (!validacion($nmb))
        return err::VALIDATION;

    // Devuelve un código de error si la película no esta en cartelera.
    if (is_null(traerPFP()) || !in_array($nmb, traerPFP()) || !checkImg($nmb, 'slider'))
        return err::NONEXISTENT;

    // Intenta eliminar la película de la cartelera, devolviendo su correspondiente código de error.
    return (borrarImg($nmb, 'slider') && eliminarSlider($nmb)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($nmb)
{
    // Valida el ID, verificando que solo contenga digitos.
    return validarStr($nmb, 40);
}