<?php

// Este script permite actualizar los datos asociados a una cuenta en particular.

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
    case NO_SESSION = 2;
    case EMPTY = 3;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en base de datos.",
            self::NO_SESSION => "La sesión no está iniciada.",
            self::EMPTY => "Todos los campos están vacios"
        };
    }
}

// Establece los campos requeridos, limpiando los vacios o no ingresados.
array_filter($_POST);
$campos = descartarVacios(['nombre', 'apellido', 'numeroCelular']);

// Guarda las variables sanitizadas en un array llamado datos.
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x);

// Devuelve el código de error correspondiente.
$error = comprobarError();
$response = ['error' => $error, 'errMsg' => $error->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError()
{
    global $datos;

    // Devuelve un código de error si la sesión no está iniciada.
    if (isset($_SESSION['user']))
        $email = $_SESSION['user'];
    else
        return err::NO_SESSION;

    // Devuelve un código de error si el id o todos los otros campos estan vacios.
    if (empty($datos))
        return err::EMPTY;

    // Intenta actualizar los datos en la base de datos y devuelve su correspondiente código de error.
    return (actUsuario($datos, $email)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

// Limpia los campos cuales estan vacios en POST.
function descartarVacios($array)
{
    $desc = [];
    foreach ($array as $x)
        if (empty($_POST[$x]))
            $desc[] = array_search($x, $array);
    foreach ($desc as $x)
        unset($array[$x]);
    return $array;
}