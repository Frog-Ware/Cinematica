<?php

// Este script guarda en la base de datos los articulos pertenecientes a un carrito.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/traer.php";
require_once "../db/insertar.php";
require_once "../config/acceso.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case NONEXISTENT = 2;
    case EMPTY = 3;
    case NOT_SET = 4;
    case NO_MATCH = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NONEXISTENT => "El carrito no existe o la sesión no está iniciada.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado.",
            self::NO_MATCH => "La cantidad de parámetros no coincide."
        };
    }
}

// Guarda las variables sanitizadas en un array llamado datos.
$campos = ['idProducto', 'cantidad'];
foreach ($campos as $x)
    $arrCampos[$x] = explode(', ', filter_input(INPUT_POST, $x));

// Devuelve el código de error correspondiente.
$error = comprobarError();
$response = ['error' => $error, 'errMsg' => $error->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError()
{
    global $campos, $arrCampos;

    // Devuelve un código de error si la sesión no está iniciada o si el carrito no existe.
    if (isset($_SESSION['user']) && traerCarrito($_SESSION['user']))
        $email = $_SESSION['user'];
    else
        return err::NONEXISTENT;

    // Devuelve un código de error si no coinciden la cantidad de IDs con valores de cantidad.
    if (count($arrCampos['idProducto']) !== count($arrCampos['cantidad']))
        return err::NO_MATCH;

    // Guarda las variables asociadas.
    for ($i = 0; $i < count($arrCampos['idProducto']); $i++)
        foreach ($arrCampos as $k => $v)
            $datos[$i][$k] = $v[$i];

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($datos as $x)
        if (!isset($x))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($arrCampos as $x)
        if (!isset($x))
            return err::EMPTY;

    // Intenta persistir el carrito en la base de datos
    return actCarritoArt($email, $datos) ?
        err::SUCCESS : err::NO_SUCCESS;
}