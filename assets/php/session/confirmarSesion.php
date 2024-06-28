<?php

// Este script devuelve los datos del usuario de estar la sesión activa. Si no es así, devuelve un error.

header("Content-Type: application/json");
session_start();
require ("../db/traer.php");

// Si hay una sesión iniciada, envia los datos del usuario via JSON. Si no es así, devuelve un error.
if(isset($_SESSION['user'])) {
    echo json_encode(traerUsuario($_SESSION['user']));
} else {
    echo json_encode(['error' => "No esta iniciada la sesión."]);
}

?>