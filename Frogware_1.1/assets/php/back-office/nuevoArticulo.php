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
            self::EXISTENT => "El producto a añadir ya existe.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado.",
            self::IMG_ERROR => "Al menos una imagen tiene un error."
        };
    }
}

// Genera una ID para el producto.
$datos['idProducto'] = generarID();

// Guarda las variables sanitizadas en un array llamado datos y los valores multiples en otro array llamado valores.
$campos = ['nombreArticulo', 'descripcion', 'precio'];
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x);

// Devuelve el código de error correspondiente.
$error = comprobarError();
$response = ['error' => $error, 'errMsg' => $error->getMsg()];
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError()
{
    global $campos, $datos;

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($campos as $x)
        if (!isset($_POST[$x]))
            return err::NOT_SET;
    if (!isset($_FILES['imagen']))
        return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x)
        if (empty($_POST[$x]))
            return err::EMPTY;
    if (empty($_FILES['imagen']))
        return err::EMPTY;

    // Devuelve un código de error si hay un artículo con el mismo nombre.
    $articuloDB = traerArticulos('nombreArticulo');
    foreach ($articuloDB as $x)
        if ($x['nombreArticulo'] == $datos['nombreArticulo'])
            return err::EXISTENT;

    // Guarda el nombre de la imagen en datos.
    $datos['imagen'] = str_replace(" ", "_", $datos['nombreArticulo'] . "_" . $x . '.webp');

    // Intenta subir la imagen a la carpeta.
    if (!subirImg($_FILES['imagen'], $datos['imagen'], 'productos'))
        return err::IMG_ERROR;

    // Intenta ingresar el artículo en la base de datos y devuelve su correspondiente código de error.
    return (nuevoArticulo($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

// Genera un ID de 11 numeros aleatorios.
function generarID()
{
    do
        $id = mt_rand(100000000, 999999999);
    while (traerArticulo($id, 'idProducto') != null);
    return $id;
}