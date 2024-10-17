<?php

// Este script devuelve un array con todas las funciones programadas de una película a futuro.

header("Content-Type: application/json; charset=utf-8");
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_FUNC = 1;
    case NONEXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case ID_NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_FUNC => "No hay funciones programadas sobre esa película.",
            self::NONEXISTENT => "No existe una película con ese ID.",
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
    // Devuelve el código de error correspondiente.
    $response = comprobar();
    echo json_encode($response);
}

function comprobar() 
{
    // Devuelve un código de error si la ID no está seteada.
    if (isset($_POST['idProducto'])) {
        $idProducto = $_POST['idProducto'];
    } else {
        return ['error' => err::ID_NOT_SET, 'errMsg' => err::ID_NOT_SET->getMsg()];
    }

    // Devuleve un código de error si la ID está vacia.
    if (blank($idProducto))
        return ['error' => err::EMPTY, 'errMsg' => err::EMPTY->getMsg()];

    // Devuelve un código de error si la ID no pasa la validación.
    if (!validacion($idProducto))
        return ['error' => err::VALIDATION, 'errMsg' => err::VALIDATION->getMsg()];
    
    // Devuelve un código de error si la función no existe.
    if (is_null(traerFuncFuturasEsp($idProducto)))
        return ['error' => err::NONEXISTENT, 'errMsg' => err::NONEXISTENT->getMsg()];

    // Intenta traer las funciones de la película y devuelve el correspondiente mensaje de error.
    $datos = traerFuncFuturasEsp($idProducto);
    return is_null($datos) ?
        ['error' => err::NO_FUNC, 'errMsg' => err::NO_FUNC->getMsg()] :
        ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'datos' => $datos];
}

function validacion($idProducto)
{
    // Valida el ID, verificando que solo contenga digitos.
    return validarInt($idProducto);
}