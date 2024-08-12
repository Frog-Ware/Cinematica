<?php

// Este script devuelve un array con todos los datos de las películas que coincidan con lo buscado.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/traer.php";
require_once "../config/acceso.php";

// Trae los datos de la pelicula por ID o nombre, según lo enviado
if (isset($_POST['idProducto']))
    $datos = traerPelicula($_POST['idProducto']);
else if (isset($_POST['nombrePelicula']))
    $datos = traerPeliculaNombre($_POST['nombrePelicula']);
else 
    $datos = null;
$response = ($datos != null) ?
    ['resultado' => $datos] : ['error' => 'No hay artículos disponibles'];

// Envía los datos mediante JSON.
echo json_encode($response);