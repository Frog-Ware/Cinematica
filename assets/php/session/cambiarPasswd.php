<?php

// Este script permite cambiar la contraseña asociada a una cuenta en particular.

header("Content-Type: application/json");
if (session_status() == PHP_SESSION_NONE) session_start();
require_once "../db/traer.php";
require_once "../db/insertar.php";

// Asigna un código de error según el caso.
enum codigoError: int{
    case SUCCESS = 0; // Procedimiento realizado con éxito.
    case NO_SUCCESS = 1; // Al menos un dato ingresado no corresponde con el resto.
    case EXISTENT = 2; // La nueva contraseña es idéntica a la anterior.
    case EMPTY = 3; // Al menos un campo está vacio.
    case NOT_SET = 4; // Al menos un campo no está asignado.
}

// Guarda las variables en un array llamado datos.
$campos = ['email', 'token', 'passwd'];
foreach ($campos as $x)
    $datos[$x] = filter_input(INPUT_POST, $x, FILTER_SANITIZE_STRING);

// Devuelve el código de error.
$response['error'] = comprobarError();
echo json_encode($response);

die();



// Funciones

function comprobarError() {
    global $campos, $datos;

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($campos as $x)
        if (!isset($_POST[$x])) return codigoError::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x)
        if (empty($_POST[$x])) return codigoError::EMPTY;

    // Devuelve un código de error si la nueva contraseña es la ya existente.
    if (password_verify($datos['passwd'], traerPasswd($datos['email']))) return codigoError::EXISTENT;

    // Intenta actualizar la contraseña en la base de datos y devuelve su correspondiente código de error.
    return ((traerToken($datos['email']) == md5($datos['token'])) && actPasswd($datos)) ?
        codigoError::SUCCESS : codigoError::NO_SUCCESS;
}