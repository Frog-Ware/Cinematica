<?php

// Este script registra una nueva película o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json");
require "../db/insertar.php";
require "../db/traer.php";

// Asigna un código de error según el caso.
enum codigoError: int{
    case SUCCESS = 0; // Procedimiento realizado con éxito.
    case NO_SUCCESS = 1; // Hubo un error en la inserción en la base de datos.
    case EXISTENT = 2; // El artículo a añadir ya está en la base de datos.
    case EMPTY = 3; // Al menos un campo está vacio.
    case NOT_SET = 4; // Al menos un campo no está asignado.
}

// Guarda las variables en un array llamado datos y los valores multiples en otro array llamado valores.
$campos = ['idProducto', 'nombreArticulo', 'descripcion', 'precio', 'imagen'];
foreach ($campos as $x)
    $datos[$x] = $_POST[$x];

// Devuelve por JSON el código de error.
$error = comprobarError();
echo json_encode(['error' => $error]);

die();


function comprobarError() {
    global $campos, $datos;

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($campos as $x)
        if (!isset($_POST[$x])) return codigoError::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x)
        if (empty($_POST[$x])) return codigoError::EMPTY;

    // Devuelve un código de error si el artículo ya existe.
    foreach (traerArticulos() as $x)
        if ($x['idProducto'] == $datos['idProducto']) return codigoError::EXISTENT;

    // Intenta ingresar el artículo en la base de datos y devuelve su correspondiente código de error.
    return (nuevoArticulo($datos)) ?
        codigoError::SUCCESS : codigoError::NO_SUCCESS;
}