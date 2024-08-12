<?php

// Este script actualiza los datos de una película o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos. Falta testear insercion en BD.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../files/subir.php";
require_once "../config/acceso.php";

// Asigna un código de error según el caso.
enum codigoError: int
{
    case SUCCESS = 0; // Procedimiento realizado con éxito.
    case NO_SUCCESS = 1; // Hubo un error en la inserción en la base de datos.
    case NONEXISTENT = 2; // La película a actualizar no existe.
    case EMPTY = 3; // Todos los campos o el campo ID estan vacios.
    case ID_NOT_SET = 4; // La ID no esta seteada.
    case IMG_ERROR = 5; // Al menos una imagen tiene un error.
}

// Establece los campos requeridos, limpiando los vacios o no ingresados.
array_filter($_POST);
$campos = descartarVacios(['actores', 'sinopsis', 'duracion', 'nombrePelicula', 'pegi', 'trailer', 'director']);
$valMultiples = descartarVacios(['categorias', 'dimensiones', 'idiomas']);
$camposImg = descartarImg(['poster', 'cabecera']);

// Guarda las variables sanitizadas en un array llamado datos y los valores multiples en otro array llamado valores.
foreach ($campos as $x) 
    $datos[$x] = filter_input(INPUT_POST, $x, FILTER_SANITIZE_STRING);
foreach ($valMultiples as $x) 
    $valores[$x] = explode(', ', filter_input(INPUT_POST, $x, FILTER_SANITIZE_STRING));

// Devuelve el código de error correspondiente.
$response['error'] = comprobarError();
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError()
{
    global $datos, $valores, $camposImg;

    // Devuelve un código de error si el id no esta seteado.
    if (isset($_POST['idProducto']))
        $idProducto = filter_input(INPUT_POST, 'idProducto', FILTER_SANITIZE_STRING);
    else
        return codigoError::ID_NOT_SET;

    // Devuelve un código de error si el id o todos los otros campos estan vacios.
    if (empty($_POST['idProducto']))
        return codigoError::EMPTY;
    if (empty($datos) && empty($valores) && empty($camposImg))
        return codigoError::EMPTY;

    // Devuelve un código de error si no existe la pelicula a actualizar.
    if (traerPelicula($idProducto) == null)
        return codigoError::NONEXISTENT;

    // Guarda el nombre de las imagenes en datos.
    $nmb = empty($datos['nombrePelicula']) ?
        traerPelicula($idProducto)['nombrePelicula'] : $datos['nombrePelicula'];
    foreach ($camposImg as $x)
        $datos[$x] = str_replace(" ", "_", $nmb . "_" . $x . '.webp');

    // Intenta subir las imagenes a la carpeta.
    foreach ($camposImg as $x)
        if (!updImg($_FILES[$x], $datos[$x], traerPelicula($idProducto)[$x], 'peliculas'))
            return codigoError::IMG_ERROR;

    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (actPelicula($datos, $valores, $idProducto)) ?
        codigoError::SUCCESS : codigoError::NO_SUCCESS;
}

// Limpia los campos cuales estan vacios en POST.
function descartarVacios($array) {
    $desc = [];
    foreach ($array as $x)
        if (empty($_POST[$x]))
            $desc[] = array_search($x, $array);
    foreach ($desc as $x) unset($array[$x]);
    return $array;
}

function descartarImg($array) {
    $desc = [];
    foreach ($array as $x)
        if ($_FILES[$x]['error'] != UPLOAD_ERR_OK)
            $desc[] = array_search($x, $array);
    foreach ($desc as $x) unset($array[$x]);
    return $array;
}