<?php

// Este script devuelve los datos del usuario de estar la sesión activa. Si no es así, devuelve un error.

header ("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE) session_start();
require_once "../db/traer.php";

// Si hay una sesión iniciada, guarda los datos del usuario en cuestión como respuesta. Si no es así, guarda un mensaje de error.
isset($_SESSION['user']) ?
    $response['datosUsuario'] = traerUsuario($_SESSION['user']) : $response['error'] = "No esta iniciada la sesión.";

// Envía la respuesta mediante JSON.
echo json_encode($response);

// Mata la ejecución.
die();