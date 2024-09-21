<?php

// Este script permite actualizar los datos asociados a una cuenta en particular.

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
    case VALIDATION = 3;
    case EMPTY = 4;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en base de datos.",
            self::NO_SESSION => "La sesión no está iniciada.",
            self::VALIDATION => "El input no pasó la validación.",
            self::EMPTY => "Todos los campos están vacios"
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Guarda las variables en un array llamado datos.
    $datos = filtrar(['nombre', 'apellido', 'numeroCelular'], $_POST);

    // Devuelve el código de error correspondiente por JSON.
    $error = comprobar($datos);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();



// Funciones

function comprobar($datos)
{
    // Devuelve un código de error si la sesión no está iniciada.
    if (isset($_SESSION['user'])) {
        $email = $_SESSION['user'];
    } else {
        return err::NO_SESSION;
    }

    // Devuelve un código de error si todos los campos estan vacios.
    if (blank($datos))
        return err::EMPTY;

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($datos))
        return err::VALIDATION;

    // Intenta actualizar los datos en la base de datos y devuelve su correspondiente código de error.
    return (actUsuario($datos, $email)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

// Devuelve un array con solo los campos solicitados que tienen algun valor.
function filtrar($claves, $arr)
{
    $arrF = [];
    foreach ($claves as $k)
        if (!blank($arr[$k]))
            $arrF[$k] = $arr[$k];
    return $arrF;
}

function validacion($datos)
{
    // Valida otros datos, verificando que solo contengan caracteres alfabéticos y su longitud este en el rango permitido.
    foreach (['nombre', 'apellido'] as $x)
        if (isset($datos[$x]) && !validarAl($datos[$x], 20))
            return false;

    // Valida el numero celular ingresado, verificando que solo contenga digitos y su longitud este en el rango permitido.
    if (isset($datos['numeroCelular']) && !validarInt($datos['numeroCelular']))
        return false;

    // Si todos los campos estan bien, retorna true.
    return true;
}