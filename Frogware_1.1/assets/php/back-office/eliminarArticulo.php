<?php

// Este script devuelve un array con todos los datos de los artículos que coincidan con lo buscado.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../files/subir.php";
require_once "../config/acceso.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case NONEXISTENT = 2;
    case EMPTY = 3;
    case ID_NOT_SET = 4;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la remoción en la base de datos.",
            self::NONEXISTENT => "El artículo a eliminar no existe.",
            self::EMPTY => "El campo ID esta vacío.",
            self::ID_NOT_SET => "La ID no esta seteada."
        };
    }
}

// Devuelve el código de error correspondiente.
$error = comprobarError();
$response = ['error' => $error, 'errMsg' => $error->getMsg()];

// Envía los datos mediante JSON.
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError()
{
    // Devuelve un código de error si el id no esta seteado.
    if (isset($_POST['idProducto']))
        $idProducto = filter_input(INPUT_POST, 'idProducto');
    else
        return err::ID_NOT_SET;

    // Devuelve un código de error si el id o todos los otros campos estan vacios.
    if (empty($idProducto))
        return err::EMPTY;

    // Devuelve un código de error si no existe el artículo a eliminar.
    $articuloDB = traerArticulo($idProducto, 'imagen');
    if ($articuloDB == null)
        return err::NONEXISTENT;

    // Intenta borrar la imagen de la carpeta.
    if (!borrarImg($articuloDB['imagen'], 'productos'))
        return err::IMG_ERROR;

    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (eliminarArticulo($idProducto)) ?
        err::SUCCESS : err::NO_SUCCESS;
}