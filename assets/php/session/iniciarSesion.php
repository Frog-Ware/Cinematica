<?php

// Este script inicia sesión o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Asigna un código de error según el caso.
enum codigoError: int
{
    case MATCH = 0; // Los valores ingresados coinciden.
    case NO_MATCH = 1; // La dirección de correo y contraseña ingresada no coinciden.
    case NO_ACCOUNT = 2; // La dirección de correo ingresada no se encuentra registrada.
    case EMPTY = 3; // Al menos un campo está vacio.
    case NOT_SET = 4; // Al menos un campo no está asignado.
    case ADMIN = 5; // La dirección de correo ingresada se encuentra registrada como administrador.
}

// Guarda las variables sanitizadas en un array llamado datos.
$campos = ['email', 'passwd'];
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x, FILTER_SANITIZE_STRING);

// Cifra la contraseña en md5.
if (!empty($datos['passwd']))
    $datos['passwd'] = md5($datos['passwd']);

// Verifica los datos e inicia sesión si se ha realizado exitosamente el registro, además de guardar los datos correspondientes como respuesta.
$response['error'] = comprobarError();
if ($response['error'] == codigoError::MATCH || $error == codigoError::ADMIN) {
    inicioSesion($datos['email']);
    $response['datosUsuario'] = traerUsuario($_SESSION['user']);
}

// Envía la respuesta mediante JSON.
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
            return codigoError::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x)
        if (empty($datos[$x]))
            return codigoError::EMPTY;

    // Devuelve un código de error si la dirección de correo no está registrada.
    if (traerPasswd($datos['email']) == null)
        return codigoError::NO_ACCOUNT;

    // Devuelve un codigo de error si la contraseña no coincide.
    if ($datos['passwd'] != traerPasswd($datos['email']))
        return codigoError::NO_MATCH;

    // Devuelve un código de error dependiendo si la cuenta es de rol Cliente o Administrador.
    return (traerRol($datos['email'])) ?
        codigoError::ADMIN : codigoError::MATCH;
}

// Inicia la sesión por 7 días.
function inicioSesion($email)
{
    $_SESSION['user'] = $email;
    session_regenerate_id(true);
    ini_set('session.gc_lifetime', 7 * 24 * 3600);
}