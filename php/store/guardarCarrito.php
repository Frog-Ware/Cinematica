<?php

// Este script guarda en la base de datos el carrito.

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
    case NOT_SET = 4;
    case NO_SEATS = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NO_SESSION => "La sesión no estaba iniciada.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado.",
            self::NO_SEATS => "Los asientos a reservar estaban ocupados."
        };
    }
}

// Guarda las variables sanitizadas en un array llamado datos.
$campos = ['idFuncion', 'asientos'];
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
    global $campos, $datos;

    // Devuelve un código de error si la sesión no está iniciada.
    if (isset($_SESSION['user']))
        $datos['email'] = $_SESSION['user'];
    else
        return err::NO_SESSION;

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($campos as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x)
        if (empty($datos[$x]))
            return err::EMPTY;

    // Verifica que los asientos a reservar no esten ya reservados.
    $asientosUsados = traerAsientos($datos['idFuncion']);
    foreach (explode(', ', $datos['asientos']) as $x)
        if (str_contains($asientosUsados, $x))
            return err::NO_SEATS;

    // Guarda los nuevos asientos ocupados en una variable.
    $actAsientos = empty($asientosUsados) ?
        $datos['asientos'] : $asientosUsados . ', ' . $datos['asientos']; 

    // Intenta persistir el carrito en la base de datos.
    return (actCarrito($datos, empty(traerCarrito($datos['email'])))) &&
        actAsientos($datos['idFuncion'], $actAsientos) ?
            err::SUCCESS : err::NO_SUCCESS;
}