<?php

// Este script permite eliminar de cartelera a una película.

ob_start();
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
    case NONEXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NONEXISTENT => "La red social a eliminar no está en la base de datos.",
            self::VALIDATION => "El campo ingresado no pasó la prueba de validación.",
            self::EMPTY => "El campo a ingresar está vacío.",
            self::NOT_SET => "El campo a ingresar no está seteado."
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

exit;



// Funciones

function main()
{
    // Devuelve el código de error correspondiente.
    $error = comprobar();
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];

    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar()
{
    // Devuelve un código de error si el nombre no esta seteado.
    if (isset($_POST['redSocial'])) {
        $nom = $_POST['redSocial'];
    } else {
        return err::NOT_SET;
    }

    // Devuelve un código de error si el nombre esta vacío.
    if (blank($nom))
        return err::EMPTY;

    // Devuelve un código de error si el nombre no pasa la validación.
    if (!validacion($nom))
        return err::VALIDATION;

    // Devuelve un código de error si la red social no está en la base de datos.
    if (!existe('redSocial', 'RedesSociales', $nom))
        return err::NONEXISTENT;

    // Intenta eliminar la red social, devolviendo su correspondiente código de error.
    return (eliminarRS($nom)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($nom)
{
    // Valida el nombre, verificando que solo contenga caracteres permitidos.
    return validarStr($nom, 20);
}