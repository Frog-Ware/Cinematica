<?php

// Este script guarda en la base de datos el carrito.

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
            self::VALIDATION => "La funcion o los asientos no pasaron la validación.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Un campo no está asignado."
        };
    }
}

// Verifica que el método utilizado sea POST.
$_SERVER['REQUEST_METHOD'] == 'POST' ?
    main() : header('HTTP/1.0 405 Method Not Allowed');

exit;



// Funciones

function main()
{
    // Guarda las variables sanitizadas en un array llamado datos.
    $datos = [];
    foreach (['idFuncion', 'asientos'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];

    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar($datos);

    $response = ['error' => $error, 'errMsg' => $error->getMsg()];

    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    echo json_encode($response);
}

function comprobar($datos)
{
    // Devuelve un código de error si la sesión no está iniciada.
    if (isset($_SESSION['user']))
        $datos['email'] = $_SESSION['user'];
    else
        return err::NO_SESSION;

    $carritoDB = array_pick(traerCarrito($datos['email']), ['idFuncion', 'asientos']);

    // Devuelve un código de error si una variable no esta seteada.
    if (!isset($datos['idFuncion']) xor !isset($datos['asientos'])) {
        return err::NOT_SET;
    } else if (isset($datos['idFuncion']) && isset($datos['asientos'])) {
        // Devuelve un código de error si una variable esta vacía.
        foreach (['idFuncion', 'asientos'] as $x)
            if (blank($datos[$x]))
                return err::EMPTY;

        // Devuelve un código de error si los asientos son inválidos o estan reservados, o si la función no existe.
        if (!validacion($datos))
            return err::VALIDATION;

        // Intenta persistir el carrito y los asientos en la base de datos.
        return (reservarAsiento($datos, $carritoDB) && actCarrito($datos, is_null($carritoDB))) ?
            err::SUCCESS : err::NO_SUCCESS;
    }
    // Intenta persistir una instancia del carrito en la base de datos.
    return (actCarrito($datos, is_null($carritoDB))) ?
            err::SUCCESS : err::NO_SUCCESS;
    
}

function validacion($datos)
{
    // Valida la id de la función, verificando que solo contenga caracteres permitidos y su longitud este en el rango permitido, ademas de que exista.
    if ((!validarInt($datos['idFuncion'])) || is_null(traerFunc($datos['idFuncion'])))
        return false;

    // Valida el formato de los asientos.
    if (!validarAsientos($datos['asientos']))
        return false;
    $sala = traerSala(traerFunc($datos['idFuncion'])['nombreCine'], traerFunc($datos['idFuncion'])['numeroSala']);
    foreach ($arr = explode(", ", $datos['asientos']) as $i) {
        // Verifica que los asientos no estén repetidos.
        if (array_count_values($arr)[$i] > 1)
            return false;
        // Valida la posición de los asientos con respecto a la capacidad de la sala.
        list($x, $y) = explode("-", $i);
        if ($x > $sala['ancho'] || $x <= 0 || $y > $sala['largo'] || $y <= 0)
            return false;
    }

    // Verifica que los asientos a reservar no esten ya reservados.
    if (traerAsientosReservados($datos['idFuncion']))
        foreach (traerAsientosReservados($datos['idFuncion']) as $x)
            if (in_array(implode('-', $x), explode(", ", $datos['asientos'])))
                return false;

    // Si todos los campos estan bien, retorna true.
    return true;
}