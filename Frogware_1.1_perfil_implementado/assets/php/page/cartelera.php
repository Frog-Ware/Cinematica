<?php

// Este script devuelve un array con todos los datos de las películas en cartelera.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Devuelve los datos de las películas de no haber errores y un código de error si no hay resultados.
$datos = empty($_POST['campos']) ?
    traerCartelera('*') : traerCartelera($_POST['campos']);
$response = ($datos != null) ?
    ['cartelera' => $datos] : ['error' => 'No hay artículos disponibles'];

// Envía los datos mediante JSON.
echo json_encode($response);

// Mata la ejecución.
die();