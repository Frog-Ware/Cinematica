<?php

// Este script devuelve un array con todos los datos de los artículos.
header("Content-Type: application/json");
require_once "../db/traer.php";

// Devuelve los datos de los artículos si no hay errores y un código de error si no hay resultados.
$datos = traerArticulos();
$response = ($datos != null) ?
    ['articulos' => $datos] : ['error' => 'No hay artículos disponibles'];

// Envía la respuesta.
echo json_encode($response);

die();