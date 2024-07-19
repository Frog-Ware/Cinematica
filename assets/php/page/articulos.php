<?php

// Este script devuelve un array con todos los datos de los artículos.
header("Content-Type: application/json");
require_once "../db/traer.php";

// Devuelve los datos de los artículos si no hay errores y un código de error si no hay resultados.
$datos = traerArticulos();
echo ($datos != null) ?
    json_encode(['articulos' => $datos]) : json_encode(['error' => 'No hay artículos disponibles']);