<?php

// Este script guarda en la base de datos el carrito.

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
    case NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NO_SESSION => "La sesión no estaba iniciada.",
            self::VALIDATION => "La funcion o los asientos no están disponibles.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Guarda las variables sanitizadas en un array llamado datos.
    $datos = [];
    foreach (['idFuncion', 'asientos'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];

    // Devuelve el código de error correspondiente mediante JSON.
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
    if (isset($_SESSION['user']))
        $datos['email'] = $_SESSION['user'];
    else
        return err::NO_SESSION;

    // Devuelve un código de error si una variable no esta seteada.
    foreach (['idFuncion', 'asientos'] as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach (['idFuncion', 'asientos'] as $x)
        if (blank($datos[$x]))
            return err::EMPTY;

    // Devuelve un código de error si los asientos son inválidos o estan reservados, o si la función no existe.
    if (!validacion($datos))
        return err::VALIDATION;

    // Guarda los nuevos asientos ocupados en una variable.
    $actAsientos = is_null(traerAsientos($datos['idFuncion'])) ?
        $datos['asientos'] : traerAsientos($datos['idFuncion']) . ', ' . $datos['asientos']; 

    // Intenta persistir el carrito en la base de datos.
    return (actCarrito($datos, is_null(traerCarrito($datos['email'])))) &&
        actAsientos($datos['idFuncion'], $actAsientos) ?
            err::SUCCESS : err::NO_SUCCESS;
}

function validacion($datos)
{
    // Valida la id de la función, verificando que solo contenga caracteres permitidos y su longitud este en el rango permitido, ademas de que exista.
    if ((!validarInt($datos['idFuncion'])) || is_null(traerFunc($datos['idFuncion'])))
        return false;

    // Organiza los datos de los asientos.
    foreach (explode(', ', $datos['asientos']) as $x) {
        if (count(explode('-', $x)) == 2) {
            [$fila, $columna] = explode('-', $x);
            $asientos[] = ['fila' => $fila, 'columna' => $columna];
        } else {
            return false;
        }
    }

    // Valida la posición de los asientos con respecto a la capacidad de la sala.
    $func = traerFunc($datos['idFuncion']);
    [$dimSala['fila'], $dimSala['columna']] = explode('x', traerCine($func['nombreCine'])['salas'][$func['numeroSala']-1]['capacidad']);
    foreach ($asientos as $x)
        foreach (['fila', 'columna'] as $y)
            if (!validarInt($x[$y]) || $x[$y] > $dimSala[$y])
                return false;

    // Verifica que los asientos a reservar no esten ya reservados.
    foreach ($asientos as $x)
        if (str_contains(traerAsientos($datos['idFuncion']), implode('-', $x)))
            return false;

    // Si todos los campos estan bien, retorna true.
    return true;
}