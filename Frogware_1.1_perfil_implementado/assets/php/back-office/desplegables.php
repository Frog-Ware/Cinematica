<?php

// Este script devuelve las listas de los valores desplegables.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Asigna los datos extraidos de la base de datos a su correspondiente posición en el array.
$datos['categorias'] = traerCategorias();
$datos['dimensiones'] = traerDimensiones();
$datos['idiomas'] = traerIdiomas();

// Envia los datos mediante JSON.
$response = (!empty($datos)) ?
    ['resultado' => $datos] : ['errMsg' => 'No hay artículos disponibles'];
echo json_encode($response);

// Mata la ejecución.
die();