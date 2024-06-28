<?php

// Este script registra una nueva película o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json");
require ("../db/insertar.php");
require ("../db/traer.php");

// Asigna un código de error según el caso.
enum codigoError: int{
    case SUCCESS = 0; // Procedimiento realizado con éxito.
    case NO_SUCCESS = 1; // Hubo un error en la inserción en la base de datos.
    case EXISTENT = 2; // La película a añadir ya está en la base de datos.
    case EMPTY = 3; // Al menos un campo está vacio.
    case NOT_SET = 4; // Al menos un campo no está asignado.
}

// Guarda las variables en un array llamado datos y los valores multiples en otro array llamado valores.
$campos = ['idProducto', 'actores', 'sinopsis', 'duracion', 'nombre', 'pegi', 'trailer', 'director', 'poster', 'cabecera'];
$valMultiples = ['categorias', 'dimensiones', 'idiomas'];
foreach ($campos as $x) $datos[$x] = $_POST[$x];
foreach ($valMultiples as $x) $valores[$x] = explode(',', $_POST[$x]);

// Devuelve por JSON el código de error.
$error = comprobarError();
echo json_encode(['error' => $error]);

die();

function comprobarError() {
    global $campos, $datos, $valMultiples, $valores;

    // Devuelve un código de error si una variable no esta seteada.
    foreach (array_merge($campos, $valMultiples) as $x) if (!isset($_POST[$x])) return codigoError::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach (array_merge($campos, $valMultiples) as $x) if (empty($_POST[$x])) return codigoError::EMPTY;

    // Devuelve un código de error si la película ya esta ingresada.
    if (traerPelicula($datos['idProducto']) != null) return codigoError::EXISTENT;

    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (nuevaPelicula($datos, $valores)) ? codigoError::SUCCESS : codigoError::NO_SUCCESS;
}

?>