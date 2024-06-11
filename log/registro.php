<?php

// Este script registra un nuevo usuario o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json");
session_start();
require ("../db/insert.php");
require ("log.php");

// Devuelve 3 si las variables estan vacías y 4 si no estan seteadas.
if (verificacionSet()){
    if (!verificacionVacio()){
        $datos = [$_POST['email'], $_POST['contraseña'], $_POST['nombre'], $_POST['apellido'], $_POST['numeroCelular'], generarToken()];
        $email = $_POST['email'];
    } else {
        echo json_encode(['error' => 3]);
        die();
    }
} else {
    echo json_encode(['error' => 4]);
    die();
}

// Verifica si el usuario no existe, y de existir, devuelve código de error 2.
if (traerContraseña($email) != null) {
    echo json_encode(['error'=> 2]);
}

// Agrega el usuario a la base de datos. Inicia sesión y devuelve 0 de ser el procedimiento exitoso, de no ser así devuelve 1.
if (nuevoUsuario($datos)) {
    echo json_encode(['error'=> 0]);
    inicioSesion($email);
} else {
    echo json_encode(['error'=> 1]);
}

// Verifica si las variables estan seteadas, devolviendo true o false segun su estado.
function verificacionSet() {
    if (isset($_POST['email']) && isset($_POST['contraseña']) && isset($_POST['nombre']) && isset($_POST['apellido']) && isset($_POST['numeroCelular'])) {
        return true;
    } else {
        return false;
    }
}

// Verifica si las variables estan vacias, devolviendo true o false segun su estado.
function verificacionVacio() {
    if (empty($_POST['email']) && empty($_POST['contraseña']) && empty($_POST['nombre']) && empty($_POST['apellido']) && empty($_POST['numeroCelular'])) {
        return true;
    } else {
        return false;
    }
}

// Genera un código de 6 caracteres aleatorios.
function generarToken() {
    $bytes = random_bytes(3);
    // Convierte los bytes en una cadena hexadecimal
    $token = bin2hex($bytes);
    return substr($token, 0, 6);
}

?>