<?php

// Este script devuelve un array con todos los datos de las películas que coincidan con lo buscado.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "No hay peliculas asociadas a ese valor."
        };
    }
}

// Trae los datos de la pelicula por ID o nombre, según lo enviado
if (isset($_POST['idProducto']))
    $datos = empty($_POST['campos']) ?
        traerPelicula($_POST['idProducto'], '*') : traerPelicula($_POST['idProducto'], $_POST['campos']);
else if (isset($_POST['nombrePelicula']))
    $datos = empty($_POST['campos']) ?
        traerPeliculaNombre($_POST['idProducto'], '*') : traerPelicula($_POST['idProducto'], $_POST['campos']);
else
    $datos = null;
$response = ($datos != null) ?
    ['error' => err::SUCCESS, 'errMsg' => err::SUCCESS->getMsg(), 'pelicula' => $datos] :
    ['error' => err::NO_SUCCESS, 'errMsg' => err::NO_SUCCESS->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();