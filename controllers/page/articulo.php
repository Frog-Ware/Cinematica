<?php

// Este script devuelve un array con todos los datos de el articulo que coincida con lo ingresado.

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
    case ID_NOT_SET = 4;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "No hay funciones asociadas a ese id.",
            self::VALIDATION => "La id no pasó la prueba de validación.",
            self::EMPTY => "Uno o mas campos están vacios.",
            self::ID_NOT_SET => "No hay ninguna id seteada."
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
    // Devuelve el código de error correspondiente.
    $idProducto = $_POST['idProducto'] ?? null;
    $error = comprobar($idProducto);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    if ($error == err::SUCCESS)
        $response['datos'] = traerArticulo($idProducto);
    
    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar($idProducto) 
{
    // Devuelve un código de error si la id no está seteada.
    if (is_null($idProducto))
        return err::ID_NOT_SET;

    // Devuleve un código de error si la id está vacía.
    if (blank($idProducto))
        return err::EMPTY;

    // Devuelve un código de error si la id no pasa la validación.
    if (!validacion($idProducto))
        return err::VALIDATION;
    
    // Intenta traer los datos de el articulo y devuelve el correspondiente mensaje de error.
    $datos = traerArticulo($idProducto);
    return is_null(traerArticulo($idProducto)) ?
        err::NO_SUCCESS : err::SUCCESS;
}

function validacion($id)
{
    // Valida el ID, verificando que solo contenga digitos.
    return validarInt($id);
}