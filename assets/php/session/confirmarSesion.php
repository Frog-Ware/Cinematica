<?php

// Este script devuelve los datos del usuario de estar la sesión activa. Si no es así, devuelve un error.

header("Content-Type: application/json");
if (session_status() == PHP_SESSION_NONE) session_start();
require_once "../db/traer.php";

// Si hay una sesión iniciada, envia los datos del usuario via JSON. Si no es así, devuelve un error.
isset($_SESSION['user']) ?
    $response ['datosUsuario'] = traerUsuario($_SESSION['user']) : $response['error'] = "No esta iniciada la sesión.";

// Envía la respuesta.
echo json_encode($response);

die();