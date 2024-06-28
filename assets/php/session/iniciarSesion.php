<?php

// Este script inicia sesión o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.
header("Content-Type: application/json");
session_start();
require ("../db/traer.php");

// Asigna un código de error según el caso.
enum codigoError: int{
    case MATCH = 0; // Los valores ingresados coinciden.
    case NO_MATCH = 1; // La dirección de correo y contraseña ingresada no coinciden.
    case NO_ACCOUNT = 2; // La dirección de correo ingresada no se encuentra registrada.
    case EMPTY = 3; // Al menos un campo está vacio.
    case NOT_SET = 4; // Al menos un campo no está asignado.
}

// Guarda las variables en un array llamado datos.
$campos = ['email', 'passwd'];
foreach ($campos as $x) $datos[$x] = $_POST[$x];

// Devuelve por JSON el código de error e inicia sesión si se ha realizado exitosamente el registro.
$error = comprobarError();
if ($error = codigoError::MATCH) inicioSesion($datos['email']);
echo json_encode(['error' => $error]);

die();

function comprobarError() {
    global $campos, $datos;
    $passwd = traerPasswd($datos['email']);

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($campos as $x) if (!isset($_POST[$x])) return codigoError::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x) if (empty($_POST[$x])) return codigoError::EMPTY;

    // Devuelve un código de error si la direccion de correo no está registrada.
    if ($passwd != null) return codigoError::NO_ACCOUNT;

    // Comprueba la coincidencia de los datos ingresados y devuelve su correspondiente código de error.
    return ($passwd == $datos['passwd']) ? codigoError::MATCH : codigoError::NO_MATCH;
}

// Inicia la sesión por 7 días.
function inicioSesion($email) {
    $_SESSION['user'] = $email;
    ini_set('session.gc_lifetime', 7*24*3600);
}

?>