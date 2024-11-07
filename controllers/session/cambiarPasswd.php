<?php

// Este script permite cambiar la contraseña asociada a una cuenta en particular.

ob_start();
header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/traer.php";
require_once "../../models/db/insertar.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case EXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Al menos un dato ingresado no corresponde con el resto.",
            self::EXISTENT => "La nueva contraseña es idéntica a la anterior.",
            self::VALIDATION => "El input no pasó la validación.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado."
        };
    }
}

$_SERVER['REQUEST_METHOD'] == 'POST' ?
    main() : header('HTTP/1.0 405 Method Not Allowed');

// Mata la ejecución.
die();



// Funciones

function main()
{
    // Guarda las variables en un array llamado datos.
    $datos = [];
    foreach (['email', 'token', 'passwd'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];

    // Devuelve el código de error correspondiente por JSON.
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
    foreach (['email', 'token', 'passwd'] as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($datos as $x)
        if (blank($x))
            return err::EMPTY;

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($datos))
        return err::VALIDATION;

    // Devuelve un código de error si la nueva contraseña es la ya existente.
    if (md5($datos['passwd']) == traerPasswd($datos['email']))
        return err::EXISTENT;

    // Intenta actualizar la contraseña en la base de datos y devuelve su correspondiente código de error.
    return ((traerToken($datos['email']) == md5($datos['token'])) && actPasswd([md5($datos['passwd']), $datos['email']])) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($datos)
{
    // Valida ciertos datos, verificando que solo contengan caracteres permitidos y su longitud este en el rango permitido.
    if (!validarStr($datos['passwd'], 12) || !validarStr($datos['token'], 6))
        return false;

    // Valida el email ingresado, verificando que este en el formato permitido y su longitud este en el rango permitido.
    if (!validarEmail($datos['email'], 50))
        return false;

    // Si todos los campos estan bien, retorna true.
    return true;
}