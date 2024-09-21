<?php

// Este script devuelve un array con todas las funciones programadas de una película a futuro.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";
require_once "../utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_FUNC = 1;
    case VALIDATION = 2;
    case EMPTY = 3;
    case NOT_SET = 4;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_FUNC => "No hay funciones programadas sobre esa película.",
            self::VALIDATION => "Un campo o mas no pasaron la prueba de validación.",
            self::EMPTY => "Uno o mas campos están vacios.",
            self::NOT_SET => "No hay ninguna clave de búsqueda asignada"
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Devuelve el código de error correspondiente.
    $response = comprobar();
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();



// Funciones

function comprobar() 
{
    // Devuelve un código de error si la ID no está seteada.
    if (isset($_POST['idProducto'])) {
        $idProducto = $_POST['idProducto'];
    } else {
        return ['error' => err::NOT_SET, 'errMsg' => err::NOT_SET->getMsg()];
    }

    // Devuleve un código de error si la ID está vacia.
    if (blank($idProducto))
        return ['error' => err::EMPTY, 'errMsg' => err::EMPTY->getMsg()];

    // Devuelve un código de error si la ID no pasa la validación.
    if (!validacion($idProducto))
        return ['error' => err::VALIDATION, 'errMsg' => err::VALIDATION->getMsg()];
    
    // Intenta traer las funciones de la película y devuelve el correspondiente mensaje de error.
    $datos = traerFunc($idProducto);
    return is_null($datos) ?
        ['error' => err::NO_FUNC, 'errMsg' => err::NO_FUNC->getMsg()] :
        ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'datos' => $datos];
}

function validacion($idProducto)
{
    // Valida el ID, verificando que solo contenga digitos.
    return validarInt($idProducto);
}