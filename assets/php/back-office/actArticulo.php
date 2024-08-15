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
            self::NONEXISTENT => "El artículo a actualizar no existe.",
            self::EMPTY => "Todos los campos o el campo ID estan vacios.",
            self::ID_NOT_SET => "La ID no esta seteada.",
            self::IMG_ERROR => "Al menos una imagen tiene un error."
        };
    }
}

// Establece los campos requeridos, limpiando los vacios o no ingresados.
array_filter($_POST);
$campos = descartarVacios(['nombreArticulo', 'descripcion', 'precio', 'imagen']);
if ($_FILES['imagen']['error'] != UPLOAD_ERR_OK)
    unset($_FILES['imagen']);

// Guarda las variables sanitizadas en un array llamado datos.
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
    global $datos, $valores, $camposImg;

    // Devuelve un código de error si el id no esta seteado.
    if (isset($_POST['idProducto']))
        $idProducto = filter_input(INPUT_POST, 'idProducto');
    else
        return err::ID_NOT_SET;

    // Devuelve un código de error si el id o todos los otros campos estan vacios.
    if (empty($idProducto))
        return err::EMPTY;
    if (empty($datos) && empty($_FILES['imagen']))
        return err::EMPTY;

    // Devuelve un código de error si no existe el artículo a actualizar.
    $articuloDB = traerArticulo($idProducto, 'nombreArticulo, imagen');
    if ($articuloDB == null)
        return err::NONEXISTENT;

    // Guarda el nombre de la imagen en datos e intenta subir la imagen a la carpeta.
    if (isset($_FILES['imagen'])) {
        $nmb = empty($datos['nombreArticulo']) ?
            $articuloDB['nombreArticulo'] : $datos['nombreArticulo'];
        $datos['imagen'] = str_replace(" ", "_", "$nmb.webp");

        if (!updImg($_FILES['imagen'], $datos['imagen'], $articuloDB['imagen'], 'peliculas'))
            return err::IMG_ERROR;
    }


    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (actArticulo($datos, $idProducto)) ?
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