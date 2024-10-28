<?php

// Este script devuelve un array con todos los asientos ocupados de una funcion específica.

header("Content-Type: application/json; charset=utf-8");
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SEATS = 1;
    case NONEXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case ID_NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SEATS => "Ningun asiento está ocupado.",
            self::NONEXISTENT => "La función no existe.",
            self::VALIDATION => "Un campo o mas no pasaron la prueba de validación.",
            self::EMPTY => "La ID esta vacía.",
            self::ID_NOT_SET => "La ID no está asignada."
        };
    }
}

// Verifica el método utilizado y envia un error 405 de no ser el permitido.
$_SERVER['REQUEST_METHOD'] == 'POST' ?
    main() : header('HTTP/1.0 405 Method Not Allowed', true, 405);

// Mata la ejecución.
die();



// Funciones

function main()
{
    // Devuelve los asientos ocupados si no hay errores y un código de error de haberlos.
    $response = comprobar();
    echo json_encode($response);
}

function comprobar() 
{
    // Devuelve un código de error si la ID no está seteada.
    if (isset($_POST['idFuncion'])) {
        $idFuncion = $_POST['idFuncion'];
    } else {
        return ['error' => err::ID_NOT_SET, 'errMsg' => err::ID_NOT_SET->getMsg()];
    }

    // Devuleve un código de error si la ID está vacia.
    if (blank($idFuncion))
        return ['error' => err::EMPTY, 'errMsg' => err::EMPTY->getMsg()];

    // Devuelve un código de error si la ID no pasa la validación.
    if (!validacion($idFuncion))
        return ['error' => err::VALIDATION, 'errMsg' => err::VALIDATION->getMsg()];

    // Devuelve un código de error si la función no existe.
    if (is_null(traerFunc($idFuncion)))
        return ['error' => err::NONEXISTENT, 'errMsg' => err::NONEXISTENT->getMsg()];
    
    // Intenta traer los asientos de la función y devuelve error si no lo logra.
    return dispAsientos($idFuncion);
}

function validacion($idFuncion)
{
    // Valida el ID, verificando que solo contenga digitos.
    return validarInt($idFuncion);
}

function dispAsientos($idFuncion)
{
    $asientosBD = traerAsientosReservados($idFuncion) ?? [[]];
    foreach ($asientosBD as $x)
        $ocup[] = implode("-", $x);
    list($ancho, $largo) = array_values(traerSala(traerFunc($idFuncion)['nombreCine'], traerFunc($idFuncion)['numeroSala']));
    for ($x = 1; $x <= $largo; $x++) {
        for ($y = 1; $y <= $ancho; $y++) {
            $pos = "$x-$y";
            $asientos[] = in_array($pos, $ocup) ?
                ['posicion' => $pos, 'disponible' => false] :
                ['posicion' => $pos, 'disponible' => true];
        }
    }
    return blank($asientosBD[0]) ?
        ['error' => err::NO_SEATS, 'errMsg' => err::NO_SEATS->getMsg(), 'datos' => $asientos] :
        ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'datos' => $asientos];
}