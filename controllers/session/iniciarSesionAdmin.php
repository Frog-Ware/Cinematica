<?php

// Este script inicia sesión o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

ob_start();
header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case MATCH = 0;
    case NO_MATCH = 1;
    case UNAUTHORIZED = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;
    case SESSION_ACT = 6;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::MATCH => "Los valores ingresados coinciden.",
            self::NO_MATCH => "La dirección de correo y contraseña ingresada no coinciden.",
            self::UNAUTHORIZED => "La dirección de correo ingresada no tiene permisos para acceder.",
            self::VALIDATION => "El input no pasó la validación.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado.",
            self::SESSION_ACT => "La sesión ya está iniciada."
        };
    }
}

// Restringe el acceso si no se utiliza el método de solicitud adecuado.
$_SERVER['REQUEST_METHOD'] == 'POST' ?
    main() : header('HTTP/1.0 405 Method Not Allowed');

exit;



// Funciones

function main()
{
    // Guarda las variables en un array llamado datos.
    $datos = [];
    foreach (['email', 'passwd'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];

    // Inicia sesión, además de guardar los datos del usuario correspondiente como respuesta. Devuelve el código de error correspondiente por JSON.
    $error = comprobar($datos);
    if ($error == err::MATCH) { 
        $response = ['error' => $error, 'errMsg' => $error->getMsg(), 'datos' => traerUsuario($datos['email'])];
        inicioSesion($datos['email']);
    } else if ($error == err::UNAUTHORIZED) {
        header('HTTP/1.1 401 Unauthorized', true, 401);
        die();
    } else {
        $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    }
    
    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar($datos)
{
    // Devuelve un código de error si una variable no esta seteada.
    foreach (['email', 'passwd'] as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($datos as $x)
        if (blank($x))
            return err::EMPTY;

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($datos))
        return err::VALIDATION;

    // Devuelve un código de error si la dirección de correo no está registrada como administrador.
    $passwd = traerPasswd($datos['email']);
    if (is_null($passwd) || traerRol($datos['email']) == 0)
        return err::UNAUTHORIZED;

    // Devuelve un código de error si la sesión está iniciada.
    if (isset($_SESSION['user']))
        return err::SESSION_ACT;

    // Inicia sesión y devuelve un código de error si la contraseña coincide y es de tipo Admin.
    return (md5($datos['passwd']) == $passwd) ?
        err::MATCH : err::NO_MATCH;
}

// Inicia la sesión por 2 horas.
function inicioSesion($email)
{
    $_SESSION['user'] = $email;
    session_regenerate_id(true);
}

function validacion($datos)
{
    // Valida la contraseña, verificando que solo contenga caracteres permitidos y su longitud este en el rango permitido.
    if (!validarStr($datos['passwd'], 12))
        return false;

    // Valida el email ingresado, verificando que este en el formato permitido y su longitud este en el rango permitido.
    if (!validarEmail($datos['email'], 50))
        return false;

    // Si todos los campos estan bien, retorna true.
    return true;
}