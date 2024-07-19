<?php

// Este script devuelve un array con todos los datos de las películas.
header("Content-Type: application/json");
require_once "../db/traer.php";

// Devuelve los datos de la película si no hay errores y un código de error si no hay resultados.
$datos = traerCartelera();
$response = ($datos != null) ?
    ['cartelera' => $datos] : ['error' => 'No hay artículos disponibles'];

// Envía la respuesta.
echo json_encode($response);

die();