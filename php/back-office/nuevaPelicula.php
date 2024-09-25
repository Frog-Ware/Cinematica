<?php

// Este script registra una nueva película o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../files/subir.php";
require_once "../utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case EXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;
    case IMG_ERR = 6;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::EXISTENT => "La película a añadir ya existe.",
            self::VALIDATION => "Un campo o mas no pasaron la prueba de validación.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado.",
            self::IMG_ERR => "Al menos una imagen tiene un error."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Genera una ID para el producto.
    $datos['idProducto'] = generarID('traerPelicula');

    // Guarda las variables en un array llamado datos y las variables múltiples en un array bidimensional llamado datosArr.
    foreach (['actores', 'sinopsis', 'duracion', 'nombrePelicula', 'pegi', 'trailer', 'director'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];
    foreach (['categorias', 'dimensiones', 'idiomas'] as $x)
        if (isset($_POST[$x]))
            $datosArr[$x] = explode(', ',$_POST[$x]);

    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar($datos, $datosArr);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();



// Funciones

function comprobar($datos, $datosArr)
{
    // Devuelve un código de error si una variable no esta seteada.
    foreach (['actores', 'sinopsis', 'duracion', 'nombrePelicula', 'pegi', 'trailer', 'director'] as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;
    foreach (['categorias', 'dimensiones', 'idiomas'] as $x)
        if (!isset($datosArr[$x]))
            return err::NOT_SET;
    foreach (['poster', 'cabecera'] as $x) {
        if (isset($_FILES[$x]) && $_FILES[$x]['error'] == UPLOAD_ERR_OK) {
            $img[$x] = $_FILES[$x];
        } else {
            return err::NOT_SET;
        }
    }

    // Devuelve un código de error si una variable esta vacía.
    foreach (array_merge($datos, $datosArr, $img) as $x)
        if (blank($x))
            return err::EMPTY;

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($datos, $datosArr, $img))
        return err::VALIDATION;

    // Devuelve un código de error si hay una película ingresada con el mismo nombre.
    $peliculaDB = traerPeliculaNombre($datos['nombrePelicula']);
    if (!is_null($peliculaDB))
        return err::EXISTENT;

    // Guarda el nombre de las imagenes en datos.
    foreach (['poster', 'cabecera'] as $x)
        $datos[$x] = str_replace(" ", "_", $datos['nombrePelicula'] . "_$x.webp");

    // Intenta subir las imagenes a la carpeta.
    foreach ($img as $k => $v)
        if (!subirImg($v, $datos[$k], 'peliculas'))
            return err::IMG_ERR;

    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (nuevaPelicula($datos, $datosArr)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($datos, $datosArr, $img)
{
    // Valida ciertos datos, verificando que solo contengan caracteres permitidos y su longitud este en el rango permitido.
    foreach (['sinopsis' => 750, 'nombrePelicula' => 50, 'pegi' => 10] as $k => $v)
        if (!validarStr($datos[$k], $v))
            return false;

    // Valida otros datos, verificando que solo contengan caracteres alfabéticos y su longitud este en el rango permitido.
    foreach (['actores' => 250, 'director' => 50] as $k => $v)
        if (!validarAl($datos[$k], $v))
            return false;

    // Valida la duracion, verificando que este en el formato permitido.
    $datos['duracion'] .= ":00";
    if (!validarHora($datos['duracion']))
        return false;
   
    // Valida la URL del trailer.
    if (!validarURL($datos['trailer'], 250))
        return false;

    // Valida el tamaño y el tipo de las imágenes.
    foreach ($img as $x)
        if (!validarImg($x, 'webp', 200))
            return false;

    // Valida los datos múltiples, verificando que existan.
    if (isset($datosArr))
        foreach ($datosArr as $k => $v)
            foreach ($v as $x) {
                $func = "traer$k";
                if (!in_array($x, array_column($func(), array_key_first($func()[0]))))
                    return false;
            }

    // Si todos los campos estan bien, retorna true.
    return true;
}