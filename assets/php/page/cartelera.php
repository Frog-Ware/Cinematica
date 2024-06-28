<?php

// Este script devuelve un array con todos los datos de las películas.
header("Content-Type: application/json");
session_start();
require ("../db/traer.php");

// Devuelve los datos de la película si no hay errores y código de error 1 si no hay resultados.
$datos = traerCartelera();
if ($datos != null) {
    echo json_encode([$datos]);
} else {
    echo json_encode(['error' => 1]);
}
?>