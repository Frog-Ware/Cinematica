<?php

// Este script inicia sesión o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json");
session_start();
require ("../db/retrieve.php");

// Devuelve 3 si las variables estan vacías y 4 si no estan seteadas.
if (isset($_POST['email']) && isset($_POST['contraseña'])){
    if (!empty($_POST['email']) && !empty($_POST['contraseña'])){
        $email = $_POST['email'];
        $contraseña = $_POST['contraseña'];
    } else {
        echo json_encode(['error' => 3]);
        die();
    }
} else {
    echo json_encode(['error' => 4]);
    die();
}

// Devuelve 0 si no hay errores, 1 si no coinciden los valores y 2 si no hay una cuenta asociada al email ingresado por el usuario.
if (traerContraseña($email) == $contraseña) {
    inicioSesion($email);
    echo json_encode(['error' => 0]);
} elseif (traerContraseña($email) != null) {
    echo json_encode(['error' => 1]);
} else {
    echo json_encode(['error' => 2]);
}

die();

// Inicia la sesión por 7 días.
function inicioSesion($email) {
    $_SESSION['user'] = $email;
    ini_set('session.gc_lifetime', 7*24*3600);
}

?>