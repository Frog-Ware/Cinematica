<?php

// Este script elimina una película según el ID ingresado.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../files/subir.php";
require_once "../utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case DB_ERR = 1;
    case NONEXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case ID_NOT_SET = 5;
    case IMG_ERR = 6;
    case REG_EXIST = 7;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::DB_ERR => "Hubo un error en la remoción en la base de datos.",
            self::NONEXISTENT => "La película a eliminar no existe.",
            self::VALIDATION => "El ID no pasó la prueba de validación.",
            self::EMPTY => "El campo ID esta vacío.",
            self::ID_NOT_SET => "La ID no esta seteada.",
            self::IMG_ERR => "Hubo un error al eliminar las imágenes.",
            self::REG_EXIST => "Hay funciones programadas de esa pelicula."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    isset($_SESSION['user']) && traerRol($_SESSION['user']) != 0 ?
        main() : header('HTTP/1.1 401 Unauthorized', true, 401);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed', true, 405);
}


// Mata la ejecución.
die();



// Funciones

function main()
{
    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar();
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
}

function comprobar()
{
    // Devuelve un código de error si el ID no esta seteado.
    if (isset($_POST['idProducto'])) {
        $idProducto = $_POST['idProducto'];
    } else {
        return err::ID_NOT_SET;
    }

    // Devuelve un código de error si el ID esta vacío.
    if (blank($idProducto))
        return err::EMPTY;

    // Devuelve un código de error si el ID no pasa la validación.
    if (!validacion($idProducto))
        return err::VALIDATION;

    // Devuelve un código de error si no existe la pelicula a eliminar.
    $peliculaDB = traerPelicula($idProducto);
    if (is_null($peliculaDB))
        return err::NONEXISTENT;

    // Devuelve un código de error si hay una función programada de la película en cuestión.
    if (traerFuncEsp($idProducto))
        return err::REG_EXIST;

    // Intenta eliminar la película de la base de datos.
    if (borrarImg($peliculaDB['poster'], 'peliculas') && borrarImg($peliculaDB['cabecera'], 'peliculas'))
        err::IMG_ERR;

    // Intenta borrar las imagenes de la carpeta y devuelve su correspondiente código de error.
    return (eliminarPelicula($idProducto)) ?
        err::SUCCESS : err::DB_ERR;
}

function validacion($idProducto)
{
    // Valida el ID, verificando que solo contenga digitos.
    return validarInt($idProducto);
}