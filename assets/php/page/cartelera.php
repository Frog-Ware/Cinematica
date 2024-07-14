<?php

// Este script devuelve un array con todos los datos de las películas.
header("Content-Type: application/json");
require "../db/traer.php";

// Devuelve los datos de la película si no hay errores y un código de error si no hay resultados.
$datos = traerCartelera();
echo ($datos != null) ?
    json_encode([$datos]) : json_encode(['error' => 'No hay películas en cartelera']);