<?php

// Este script devuelve un array con todos los datos de las películas que coincidan con lo buscado.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";
require_once "../utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case VALIDATION = 2;
    case EMPTY = 3;
    case NOT_SET = 4;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "No hay coincidencia.",
            self::VALIDATION => "El input no pasó la validación.",
            self::EMPTY => "El input estaba vacío",
            self::NOT_SET => "El input no estaba seteado"
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Devuelve los datos de la búsqueda si no hay errores y un código de error si no hay resultados.
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
    // Devuelve un código de error si el input no está seteado.
    if (isset($_POST['busqueda'])) {
        $clave = $_POST['busqueda'];
    } else {
        return ['error' => err::NOT_SET, 'errMsg' => err::NOT_SET->getMsg()];
    }

    // Devuleve un código de error si ambas claves de búsqueda están vacías.
    if (blank($clave))
        return ['error' => err::EMPTY, 'errMsg' => err::EMPTY->getMsg()];

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($clave))
        return ['error' => err::VALIDATION, 'errMsg' => err::VALIDATION->getMsg()];
    
    // Intenta traer los datos de la película y devuelve el correspondiente mensaje de error.
    $datos = traerBusqueda("%$clave%");
    return is_null($datos) ?
        ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()] :
        ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'datos' => $datos];
}

function validacion($clave)
{
    // Valida el ID, verificando que solo contenga digitos.
    return validarStr($clave, 50);
}