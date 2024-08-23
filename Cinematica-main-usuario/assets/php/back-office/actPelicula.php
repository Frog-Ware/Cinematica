<?php

// Este script actualiza los datos de una película o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos. Falta testear insercion en BD.

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
    case IMG_ERROR = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NONEXISTENT => "La película a actualizar no existe.",
            self::EMPTY => "Todos los campos o el campo ID estan vacios.",
            self::ID_NOT_SET => "La ID no esta seteada.",
            self::IMG_ERROR => "Al menos una imagen tiene un error."
        };
    }
}

// Establece los campos requeridos, limpiando los vacios o no ingresados.
array_filter($_POST);
$campos = descartarVacios(['actores', 'sinopsis', 'duracion', 'nombrePelicula', 'pegi', 'trailer', 'director']);
$valMultiples = descartarVacios(['categorias', 'dimensiones', 'idiomas']);
$camposImg = descartarImg(['poster', 'cabecera']);

// Guarda las variables sanitizadas en un array llamado datos y los valores multiples en otro array llamado valores.
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x);
foreach ($valMultiples as $x)
    $valores[$x] = explode(', ', filter_input(INPUT_POST, $x));

// Devuelve el código de error correspondiente.
$error = comprobarError();
$response = ['error' => $error, 'errMsg' => $error->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError()
{
    global $datos, $valores, $camposImg;

    // Devuelve un código de error si el id no esta seteado.
    if (isset($_POST['idProducto']))
        $idProducto = filter_input(INPUT_POST, 'idProducto');
    else
        return err::ID_NOT_SET;

    // Devuelve un código de error si el id o todos los otros campos estan vacios.
    if (empty($idProducto))
        return err::EMPTY;
    if (empty($datos) && empty($valores) && empty($camposImg))
        return err::EMPTY;

    // Devuelve un código de error si no existe la pelicula a actualizar.
    $peliculaDB = traerPelicula($idProducto, 'nombrePelicula, poster, cabecera');
    if ($peliculaDB == null)
        return err::NONEXISTENT;

    // Guarda el nombre de las imagenes en datos.
    $nmb = empty($datos['nombrePelicula']) ?
        $peliculaDB['nombrePelicula'] : $datos['nombrePelicula'];
    foreach ($camposImg as $x)
        $datos[$x] = str_replace(" ", "_", $nmb . "_" . $x . '.webp');

    // Intenta subir las imagenes a la carpeta.
    foreach ($camposImg as $x)
        if (!updImg($_FILES[$x], $datos[$x], $peliculaDB[$x], 'peliculas'))
            return err::IMG_ERROR;

    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (actPelicula($datos, $valores, $idProducto)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

// Limpia los campos cuales estan vacios en POST.
function descartarVacios($array)
{
    $desc = [];
    foreach ($array as $x)
        if (empty($_POST[$x]))
            $desc[] = array_search($x, $array);
    foreach ($desc as $x)
        unset($array[$x]);
    return $array;
}

function descartarImg($array)
{
    $desc = [];
    foreach ($array as $x)
        if ($_FILES[$x]['error'] != UPLOAD_ERR_OK)
            $desc[] = array_search($x, $array);
    foreach ($desc as $x)
        unset($array[$x]);
    return $array;
}