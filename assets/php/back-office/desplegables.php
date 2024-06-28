<?php

// Este script devuelve las listas de los valores desplegables.

header("Content-Type: application/json");
require ("../db/traer.php");

$datos[] = traerCategorias();
$datos[] = traerDimensiones();
$datos[] = traerIdiomas();

echo json_encode($datos);

?>