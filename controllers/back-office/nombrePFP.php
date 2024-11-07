<?php

// Este script cambia el nombre de una foto de perfil en la base de datos.

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
    case VALIDATION = 2;
    case EMPTY = 3;
    case NOT_SET = 4;
    case IMG_ERR = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::VALIDATION => "El nombre no pasó la prueba de validación.",
            self::EMPTY => "Un campo está vacio.",
            self::NOT_SET => "Un campo no está asignado.",
            self::IMG_ERR => "La imagen tiene un error."
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
    // Guarda las variables en un array llamado datos.
    foreach (['nombreNuevo', 'nombreViejo'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = str_replace(' ', '_', $_POST['nombre']) . ".webp";

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
    foreach ($datos as $x)
        if (!isset($x))
            return err::NOT_SET;

    foreach ($datos as $x)
        if (blank($x))
            return err::EMPTY;

    foreach ($datos as $x)
        if (!validacion($x))
            return err::VALIDATION;
    
    if (!actNombreImg($datos['nombreNuevo'], $datos['nombreViejo'], 'perfil'))
        return err::IMG_ERR;

    // Intenta ingresar el nombre de la imagen en la base de datos y devuelve su correspondiente código de error.
    return (actPFP($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($nmb, $img)
{
    // Valida que el nombre de la imagen tenga los caracteres permitidos.
    return validarStr($nmb, 40);
}