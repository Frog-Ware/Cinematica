<?php

// Este script cierra la sesión y envía un código de error.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../config/acceso.php";

// Si hay una sesión iniciada, la cierra.
if (isset($_SESSION['user']))
    session_destroy();

// Mata la ejecución.
die();