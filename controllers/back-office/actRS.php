<?php

// Este script actualiza el link hacia una red social en la base de datos.

ob_start();
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
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NONEXISTENT => "La RS no está en la base de datos",
            self::VALIDATION => "Un campo no pasó la prueba de validación.",
            self::EMPTY => "Un campo está vacio.",
            self::NOT_SET => "Un campo no está asignado."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    isset($_SESSION['user']) && traerRol($_SESSION['user']) != 0 ?
        main() : header('HTTP/1.1 401 Unauthorized', true, 401);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

exit;



// Funciones

function main()
{
    // Devuelve el código de error correspondiente mediante JSON.
    foreach (['urlRS', 'redSocial'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];

    $error = comprobar($datos);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    
    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar($datos)
{
    // Verifica que todos los campos están seteados.
    foreach (['urlRS', 'redSocial'] as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Verifica que ningún campo esté vacío.
    foreach (['redSocial', 'urlRS'] as $x)
        if (blank($datos[$x]))
            return err::EMPTY;

    // Valida los valores ingresados.
    if (!validacion($datos))
        return err::VALIDATION;

    // Verifica que la red social tenga un registro en la base de datos.
    if (!existe('redSocial', 'RedesSociales', $datos['redSocial']))
        return err::NONEXISTENT;

    // Actualiza el link de la RS en la base de datos y devuelve su correspondiente código de error.
    return (actRS($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($datos)
{
    // Valida que el nombre de la RS tenga los caracteres permitidos.
    if (!validarStr($datos['redSocial'], 20))
        return false;

    // Valida que el link tenga los caracteres permitidos.
    if (!validarURL($datos['urlRS'], 250))
        return false;

    return true;
}