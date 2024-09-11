<?php

// Este script permite cambiar la contraseña asociada a una cuenta en particular.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/traer.php";
require_once "../db/insertar.php";

// Asigna un código de error según el caso.
enum err: int
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
            self::NO_SUCCESS => "Al menos un dato ingresado no corresponde con el resto.",
            self::EXISTENT => "La nueva contraseña es idéntica a la anterior.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado."
        };
    }
}

// Guarda las variables sanitizadas en un array llamado datos.
$campos = ['email', 'token', 'passwd'];
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x);

// Cifra la nueva contraseña y el token en md5.
if (!empty($datos['passwd']))
    $datos['passwd'] = md5($datos['passwd']);

// Devuelve el código de error correspondiente.
$error = comprobarError();
$response = ['error' => $error, 'errMsg' => $error->getMsg()];
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

    // Devuelve un código de error si la nueva contraseña es la ya existente.
    if ($datos['passwd'] == traerPasswd($datos['email']))
        return err::EXISTENT;

    // Intenta actualizar la contraseña en la base de datos y devuelve su correspondiente código de error.
    return ((traerToken($datos['email']) == md5($datos['token'])) && actPasswd($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
}