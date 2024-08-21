<?php

// Este script registra una nueva película o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

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
    case EXISTENT = 2;
    case EMPTY = 3;
    case NOT_SET = 4;
    case IMG_ERROR = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::EXISTENT => "La película a añadir ya existe.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado.",
            self::IMG_ERROR => "Al menos una imagen tiene un error."
        };
    }
}



// Establece los campos requeridos.
$campos = ['actores', 'sinopsis', 'duracion', 'nombrePelicula', 'pegi', 'trailer', 'director'];
$valMultiples = ['categorias', 'dimensiones', 'idiomas'];
$camposImg = ['poster', 'cabecera'];
$totalCampos = array_merge($campos, $valMultiples);

// Genera una ID para la película.
$datos['idProducto'] = generarID();

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
    global $totalCampos, $datos, $valores, $camposImg;

    // Devuelve un código de error si una variable no esta seteada.
    foreach (array_merge($totalCampos) as $x)
        if (!isset(array_merge($datos, $valores)[$x]))
            return err::NOT_SET;
    foreach ($camposImg as $x)
        if (!isset($_FILES[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach (array_merge($totalCampos) as $x)
        if (empty(array_merge($datos, $valores)[$x]))
            return err::EMPTY;
    foreach ($camposImg as $x)
        if ($_FILES[$x]['error'] != UPLOAD_ERR_OK)
            return err::EMPTY;

    // Devuelve un código de error si hay una película ingresada con el mismo nombre y director.
    $peliculaDB = traerPeliculaNombre($datos['nombrePelicula'], 'director');
    if ($peliculaDB != null && $peliculaDB['director'] == $datos['director'])
        return err::EXISTENT;

    // Guarda el nombre de las imagenes en datos.
    foreach ($camposImg as $x)
        $datos[$x] = str_replace(" ", "_", $datos['nombrePelicula'] . "_" . $x . '.webp');

    // Intenta subir las imagenes a la carpeta.
    foreach ($camposImg as $x)
        if (!subirImg($_FILES[$x], $datos[$x], 'peliculas'))
            return err::IMG_ERROR;

    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (nuevaPelicula($datos, $valores)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

// Genera un ID de 11 numeros aleatorios.
function generarID()
{
    do
        $id = mt_rand(100000000, 999999999);
    while (traerPelicula($id, 'idProducto') != null);
    return $id;
}