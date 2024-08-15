<?php

// Este script devuelve un array con todos los datos de las películas que coincidan con lo buscado.

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
    case IMG_ERR = 5;
    case REG_EXIST = 6;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la remoción en la base de datos.",
            self::NONEXISTENT => "La película a eliminar no existe.",
            self::EMPTY => "El campo ID esta vacío.",
            self::ID_NOT_SET => "La ID no esta seteada.",
            self::IMG_ERR => "Hubo un error eliminando las imágenes.",
            self::REG_EXIST => "Existe un registro de compras con esa película."
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

    // Devuelve un código de error si no existe la pelicula a eliminar.
    $peliculaDB = traerPelicula($idProducto, 'poster, cabecera');
    if ($peliculaDB == null)
        return err::NONEXISTENT;

    // Intenta borrar las imagenes de la carpeta.
    if (!borrarImg($peliculaDB['poster'], 'peliculas') || !borrarImg($peliculaDB['cabecera'], 'peliculas'))
        return err::IMG_ERR;

    // Si hay registro de compras existente con esa ID, devuelve un error.
    if (traerRegistro($idProducto))
        return err::REG_EXIST;

    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (eliminarPelicula($idProducto)) ?
        err::SUCCESS : err::NO_SUCCESS;
}