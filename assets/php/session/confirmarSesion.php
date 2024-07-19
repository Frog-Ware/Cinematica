<?php

// Este script devuelve los datos del usuario de estar la sesión activa. Si no es así, devuelve un error.

header("Content-Type: application/json");
if (session_status() == PHP_SESSION_NONE) session_start();
require_once "../db/traer.php";

// Si hay una sesión iniciada, envia los datos del usuario via JSON. Si no es así, devuelve un error.
echo isset($_SESSION['user']) ?
    json_encode(['datosUsuario' => traerUsuario($_SESSION['user'])]) : json_encode(['error' => "No esta iniciada la sesión."]);
