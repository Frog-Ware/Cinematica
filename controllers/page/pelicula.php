<?php

// Este script devuelve un array con todos los datos de las películas que coincidan con lo ingresado.

ob_start();
header("Content-Type: application/json; charset=utf-8");
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";

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
            self::NO_SUCCESS => "No hay peliculas asociadas a ese valor.",
            self::VALIDATION => "Un campo o mas no pasaron la prueba de validación.",
            self::EMPTY => "Uno o mas campos están vacios.",
            self::NOT_SET => "No hay ninguna clave de búsqueda asignada"
        };
    }
}

// Verifica el método utilizado y envia un error 405 de no ser el permitido.
$_SERVER['REQUEST_METHOD'] == 'POST' ?
    main() : header('HTTP/1.0 405 Method Not Allowed', true, 405);

exit;



// Funciones

function main()
{
    $n = false;
    // Asigna la clave referida a la búsqueda.
    if (isset($_POST['idProducto']) && !blank($_POST['idProducto'])) {
        $clave = $_POST['idProducto'];
    } else if (isset($_POST['nombrePelicula']) && !blank($_POST['nombrePelicula'])){
        $clave = $_POST['nombrePelicula'];
        $n = true;
    } else {
        $clave = null;
    }

    // Devuelve el código de error correspondiente.
    $response = comprobar($clave, $n);
    
    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar($clave, $n) 
{
    // Devuelve un código de error si ninguna clave de búsqueda está seteada.
    if (!isset($clave))
        return ['error' => err::NOT_SET, 'errMsg' => err::NOT_SET->getMsg()];

    // Devuleve un código de error si ambas claves de búsqueda están vacías.
    if (blank($clave))
        return ['error' => err::EMPTY, 'errMsg' => err::EMPTY->getMsg()];

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($clave, $n))
        return ['error' => err::VALIDATION, 'errMsg' => err::VALIDATION->getMsg()];
    
    // Intenta traer los datos de la película y devuelve el correspondiente mensaje de error.
    $datos = $n ?
        traerPeliculaNombre($clave) : traerPelicula($clave);
    return is_null($datos) ?
        ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()] :
        ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'datos' => $datos];
}

function validacion($clave, $n)
{
    // Valida el ID, verificando que solo contenga digitos.
    return isset($n) ?
        validarStr($clave, 50) : validarInt($clave);
}