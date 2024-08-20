<?php

// Este script inicia sesión o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Asigna un código de error según el caso.
enum err: int
{
    case MATCH = 0;
    case NO_MATCH = 1;
    case NO_ACCOUNT = 2;
    case EMPTY = 3;
    case NOT_SET = 4;
    case ADMIN = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::MATCH => "Los valores ingresados coinciden.",
            self::NO_MATCH => "La dirección de correo y contraseña ingresada no coinciden.",
            self::NO_ACCOUNT => "La dirección de correo ingresada no se encuentra registrada.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado."
        };
    }
}

// Guarda las variables sanitizadas en un array llamado datos.
$campos = ['email', 'passwd'];
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x);

// Cifra la contraseña en md5.
if (!empty($datos['passwd']))
    $datos['passwd'] = md5($datos['passwd']);

// Verifica los datos e inicia sesión si se ha realizado exitosamente el registro, además de guardar los datos correspondientes como respuesta.
$error = comprobarError();
$response = ['error' => $error, 'errMsg' => $error->getMsg()];
if ($error == err::MATCH || $error == err::ADMIN) {
    inicioSesion($datos['email']);
    $response['datosUsuario'] = traerUsuario($_SESSION['user']);
}
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError()
{
    global $campos, $datos;

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($campos as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x)
        if (empty($datos[$x]))
            return err::EMPTY;

    // Devuelve un código de error si la dirección de correo no está registrada.
    if (traerPasswd($datos['email']) == null)
        return err::NO_ACCOUNT;

    // Devuelve un codigo de error si la contraseña no coincide.
    if ($datos['passwd'] != traerPasswd($datos['email']))
        return err::NO_MATCH;

    // Devuelve un código de error dependiendo si la cuenta es de rol Cliente o Administrador.
    return (traerRol($datos['email'])) ?
        err::ADMIN : err::MATCH;
}

// Inicia la sesión por 7 días.
function inicioSesion($email)
{
    $_SESSION['user'] = $email;
    session_regenerate_id(true);
    ini_set('session.gc_lifetime', 7 * 24 * 3600);
}