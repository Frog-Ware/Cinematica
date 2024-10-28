<?php

// Este script guarda en la base de datos los articulos pertenecientes a un carrito.

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
    case NONEXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NO_SESSION => "La sesión no está iniciada.",
            self::VALIDATION => "Uno de los campos no paso la prueba de validación.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Guarda las variables sanitizadas en un array llamado d.
    foreach (['idProducto', 'cantidad'] as $x)
        if (isset($_POST[$x]))
            $d[$x] = explode(', ', $_POST[$x]);

    // Ordena los datos.
    $datos = [];
    for ($i = 0; $i < count($d['idProducto']); $i++)
        foreach ($d as $k => $v)
            $datos[$i][$k] = $v[$i];

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
    if (isset($_SESSION['user'])) {
        $email = $_SESSION['user'];
    } else {
        return err::NO_SESSION;
    }

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($datos as $x)
        foreach (['idProducto', 'cantidad'] as $y)
            if (!isset($x[$y]))
                return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($datos as $x)
        foreach ($x as $xx)
            if (blank($x))
                return err::EMPTY;
    
    if (!validacion($datos))
        return err::VALIDATION;

    // Intenta persistir el carrito en la base de datos
    return actCarritoArt($email, $datos) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($datos)
{
    foreach ($datos as $x)
        if (!validarInt($x['idProducto']) || is_null(traerArticulo($x['idProducto'])) ||!validarInt($x['cantidad']))
            return false;
    // Si todos los campos estan bien, retorna true.
    return true;
}