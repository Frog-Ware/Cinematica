<?php

// Este script añade la pelicula deseada a la cartelera.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/insertar.php";
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case EXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case ID_NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::EXISTENT => "La película a añadir ya está en la cartelera.",
            self::VALIDATION => "El ID no pasó la prueba de validación.",
            self::EMPTY => "El ID está vacío.",
            self::ID_NOT_SET => "El ID no está seteado."
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



// Funciones

function main()
{
    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar();
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
}

function comprobar()
{
    // Devuelve un código de error si el id no esta seteado.
    if (isset($_POST['idProducto'])) {
        $idProducto = $_POST['idProducto'];
    } else {
        return err::ID_NOT_SET;
    }

    // Devuelve un código de error si el ID esta vacío.
    if (blank($idProducto))
        return err::EMPTY;

    // Devuelve un código de error si el ID no pasa la validación.
    if (!validacion($idProducto))
        return err::VALIDATION;

    // Devuelve un código de error si la película no esta en cartelera.
    if (in_array($idProducto, array_column(traerIdCartelera(), 'idProducto')))
        return err::EXISTENT;

    // Verifica que la película exista e intenta ingresar la película en la cartelera, devolviendo su correspondiente código de error.
    return (!is_null(traerPelicula($idProducto)) && nuevaEnCartelera($idProducto)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($idProducto)
{
    // Valida el ID, verificando que solo contenga digitos.
    return validarInt($idProducto);
}