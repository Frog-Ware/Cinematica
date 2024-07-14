<?php

// Este script devuelve un array con todos los datos de los artículos.
header("Content-Type: application/json");
require "../db/traer.php";

// Devuelve los datos de los artículos si no hay errores y un código de error si no hay resultados.
$datos = traerArticulos();
echo ($datos != null) ?
    json_encode($datos) : json_encode(['error' => 'No hay artículos disponibles']);