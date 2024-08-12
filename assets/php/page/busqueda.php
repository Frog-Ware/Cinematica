<?php

// Este script devuelve un array con todos los datos de las películas que coincidan con lo buscado.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Asigna el valor de búsqueda a una variable.
$var = "%" . filter_input(INPUT_POST, 'busqueda', FILTER_SANITIZE_STRING) . "%";
$datos = traerBusqueda($var);
$response = ($datos != null) ?
    ['resultado' => $datos] : ['error' => 'No hay artículos disponibles'];

// Envía los datos mediante JSON.
echo json_encode($response);