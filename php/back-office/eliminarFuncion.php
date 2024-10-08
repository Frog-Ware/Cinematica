<?php

// Este script elimina una función según el ID ingresado.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../files/subir.php";
require_once "../utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case DB_ERR = 1;
    case NONEXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case ID_NOT_SET = 5;
    case REG_EXIST = 6;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::DB_ERR => "Hubo un error en la remoción en la base de datos.",
            self::NONEXISTENT => "La función a eliminar no existe.",
            self::VALIDATION => "El ID no pasó la prueba de validación.",
            self::EMPTY => "El campo ID esta vacío.",
            self::ID_NOT_SET => "La ID no esta seteada.",
            self::REG_EXIST => "Existe un registro de compras con esa película."
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
    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar();
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
}

function comprobar()
{
    // Devuelve un código de error si el ID no esta seteado.
    if (isset($_POST['idFuncion'])) {
        $idFuncion = $_POST['idFuncion'];
    } else {
        return err::ID_NOT_SET;
    }

    // Devuelve un código de error si el ID esta vacío.
    if (blank($idFuncion))
        return err::EMPTY;

    // Devuelve un código de error si el ID no pasa la validación.
    if (!validacion($idFuncion))
        return err::VALIDATION;

    // Devuelve un código de error si no existe la pelicula a eliminar.
    $func = traerFunc($idFuncion);
    if (is_null($func))
        return err::NONEXISTENT;

    // Devuelve un código de error si hay un registro de compras existente con esa ID.
    if (traerRegistro($idFuncion))
        return err::REG_EXIST;

    // Intenta eliminar la película de la base de datos y devuelve su correspondiente código de error.
    return (eliminarFunc($idFuncion)) ?
        err::SUCCESS : err::DB_ERR;
}

function validacion($idFuncion)
{
    // Valida el ID, verificando que solo contenga digitos.
    return validarInt($idFuncion);
}