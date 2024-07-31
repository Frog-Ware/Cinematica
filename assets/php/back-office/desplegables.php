<?php

// Este script devuelve las listas de los valores desplegables.

header ("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";

// Asigna los datos extraidos de la base de datos a su correspondiente posición en el array.
$datos['categorias'] = traerCategorias();
$datos['dimensiones'] = traerDimensiones();
$datos['idiomas'] = traerIdiomas();

// Envia los datos mediante JSON.
$response['desplegables'] = $datos;
echo json_encode($response);

// Mata la ejecución.
die();