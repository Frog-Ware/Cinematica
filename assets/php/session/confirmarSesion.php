<?php

// Este script devuelve los datos del usuario de estar la sesión activa. Si no es así, devuelve un error.

header("Content-Type: application/json");
session_start();
require "../db/traer.php";

// Si hay una sesión iniciada, envia los datos del usuario via JSON. Si no es así, devuelve un error.
echo isset($_SESSION['user']) ?
    json_encode(traerUsuario($_SESSION['user'])) : json_encode(['error' => "No esta iniciada la sesión."]);