<?php

// Este script registra un nuevo usuario o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Asigna un código de error según el caso.
enum codigoError: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case EXISTENT = 2;
    case EMPTY = 3;
    case NOT_SET = 4;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::EXISTENT => "El email a registrar ya está en la base de datos.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado."
        };
    }
}

// Guarda las variables sanitizadas en un array llamado datos.
$campos = ['email', 'nombre', 'apellido', 'imagenPerfil', 'passwd', 'numeroCelular'];
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x);

// Cifra la contraseña y el token generado en md5.
if (!empty($datos['passwd']))
    $datos['passwd'] = md5($datos['passwd']);
$token = generarToken();
$datos['token'] = md5($token);

// Verifica los datos e inicia sesión si se ha realizado exitosamente el registro, además de guardar los datos correspondientes como respuesta.
$response['error'] = comprobarError();
if ($response['error'] == codigoError::SUCCESS) {
    inicioSesion($datos['email']);
    $response['datosUsuario'] = traerUsuario($_SESSION['user']);
    $response['token'] = $token;
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

    // Devuelve un código de error si el usuario ya esta registrado.
    if (traerPasswd($datos['email']) != null)
        return codigoError::EXISTENT;

    // Intenta registrar al usuario en la base de datos y devuelve su correspondiente código de error.
    return (nuevoCliente($datos)) ?
        codigoError::SUCCESS : codigoError::NO_SUCCESS;
}

// Genera un código de 6 caracteres aleatorios.
function generarToken()
{
    $bytes = random_bytes(3);
    // Convierte los bytes en una cadena hexadecimal
    $token = bin2hex($bytes);
    return substr($token, 0, 6);
}

// Inicia la sesión por 7 días.
function inicioSesion($email)
{
    $_SESSION['user'] = $email;
    session_regenerate_id(true);
    ini_set('session.gc_lifetime', 7 * 24 * 3600);
}