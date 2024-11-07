<?php

// Este script cambia el precio de un tipo de películas.

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
    case VALIDATION = 2;
    case EMPTY = 3;
    case NOT_SET = 4;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::VALIDATION => "Un campo no pasó la prueba de validación.",
            self::EMPTY => "Un campo está vacío.",
            self::NOT_SET => "Un campo no está seteado."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    isset($_SESSION['user']) && traerRol($_SESSION['user']) == 2 ?
        main() : header('HTTP/1.1 401 Unauthorized', true, 401);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed', true, 405);
}

exit;



// Funciones

function main()
{
    // Guarda las variables en un array llamado datos.
    foreach (['dimension', 'precio'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];

    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar($datos);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    
    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar($datos)
{
    // Devuelve un código de error si una variable no esta seteada.
    foreach (['dimension', 'precio'] as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach (['dimension', 'precio'] as $x)
        if (blank($datos[$x]))
            return err::EMPTY;

    // Devuelve un código de error si el email no pasa la validación.
    if (!validacion($datos))
        return err::VALIDATION;

    // Intenta cambiar el rol al usuario.
    return (cambiarPrecio($datos['dimension'], $datos['precio'])) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($datos)
{
    // Valida el precio, verificando que solo contenga dígitos.
    if (!validarInt($datos['precio']))
        return false;

    // Verifica que la dimension exista.
    if (!in_array($datos['dimension'], traerDimensiones()))
        return false;

    return true;
}