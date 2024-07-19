<?php

// Este script devuelve las listas de los valores desplegables.

header ("Content-Type: application/json");
require_once "../db/traer.php";

$datos['categorias'] = traerCategorias();
$datos['dimensiones'] = traerDimensiones();
$datos['idiomas'] = traerIdiomas();

// Devuelve el código de error.
$response['desplegables'] = $datos;
echo json_encode($response);

die();