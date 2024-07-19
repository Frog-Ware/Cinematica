<?php

// Este script devuelve las listas de los valores desplegables.

header ("Content-Type: application/json");
require_once "../db/traer.php";

$datos[] = traerCategorias();
$datos[] = traerDimensiones();
$datos[] = traerIdiomas();

echo json_encode(['desplegables' => $datos]);