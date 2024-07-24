<?php

// Este script registra una nueva película o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../files/subir.php";

// Asigna un código de error según el caso.
enum codigoError: int{
    case SUCCESS = 0; // Procedimiento realizado con éxito.
    case NO_SUCCESS = 1; // Hubo un error en la inserción en la base de datos.
    case EXISTENT = 2; // La película a añadir ya está en la base de datos.
    case EMPTY = 3; // Al menos un campo está vacio.
    case NOT_SET = 4; // Al menos un campo no está asignado.
    case IMG_ERROR = 5; // Al menos una imagen tiene un error.
}


// Genera una ID para la película.
$datos['idProducto'] = generarID();

// Guarda las variables sanitizadas en un array llamado datos.
$campos = ['actores', 'sinopsis', 'duracion', 'nombrePelicula', 'pegi', 'trailer', 'director'];
$valMultiples = ['categorias', 'dimensiones', 'idiomas'];
$camposImg = ['poster', 'cabecera'];
$totalCampos = array_merge($campos, $valMultiples);
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x, FILTER_SANITIZE_STRING);
foreach ($valMultiples as $x)
    $valores[$x] = explode(', ', filter_input(INPUT_POST, $x, FILTER_SANITIZE_STRING));

// Guarda el nombre de las imagenes en datos.
foreach ($camposImg as $x) {
    $ext = ($_FILES[$x]['type'] == "image/jpeg") ?
        '.jpg' : '.png';
    $nom = $datos['nombrePelicula'] . "_" . $x . $ext;
    $datos[$x] = str_replace(" ", "_", $nom);
}

// Devuelve el código de error correspondiente.
$response['error'] = comprobarError();
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError() {
    global $totalCampos, $datos, $valores, $camposImg;

    // Devuelve un código de error si una variable no esta seteada.
    foreach (array_merge($totalCampos) as $x)
        if (!isset(array_merge($datos, $valores)[$x])) return codigoError::NOT_SET;
    foreach ($camposImg as $x)
        if (!isset($_FILES[$x])) return codigoError::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach (array_merge($totalCampos) as $x)
        if (empty(array_merge($datos, $valores)[$x])) return codigoError::EMPTY;
    foreach ($camposImg as $x)
        if (empty($_FILES[$x])) return codigoError::EMPTY;

    // Devuelve un código de error si hay una película ingresada con el mismo nombre y director.
    $comp = traerPeliculaNombre($datos['nombrePelicula']);
    if ($comp != null && $comp['director'] == $datos['director'])
        return codigoError::EXISTENT;

    // Intenta subir las imagenes a la carpeta.
    foreach ($camposImg as $x)
        if (!subirImg($_FILES[$x], $datos[$x])) return codigoError::IMG_ERROR;
        
    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (nuevaPelicula($datos, $valores)) ?
        codigoError::SUCCESS : codigoError::NO_SUCCESS;
}

// Genera un ID de 11 numeros aleatorios.
function generarID() {
    do $id = mt_rand(100000000, 999999999);
        while (traerPelicula($id) != null);
    return $id;
}