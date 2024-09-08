<?php

// Este script devuelve un array con todos los datos de los artículos.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Devuelve los datos de los artículos si no hay errores y un código de error si no hay resultados.
$datos = empty($_POST['campos']) ?
    traerArticulos('*') : traerArticulos($_POST['campos']);
$response = ($datos != null) ?
    ['articulos' => $datos] : ['error' => 'No hay artículos disponibles'];

// Envía los datos mediante JSON.
echo json_encode($response);

// Mata la ejecución.
die();