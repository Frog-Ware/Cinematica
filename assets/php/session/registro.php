<?php

// Este script registra un nuevo usuario o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json");
session_start();
require "../db/insertar.php";
require "../db/traer.php";

// Asigna un código de error según el caso.
enum codigoError: int {
    case SUCCESS = 0; // Procedimiento realizado con éxito.
    case NO_SUCCESS = 1; // Hubo un error en la inserción en la base de datos.
    case EXISTENT = 2; // El email a registrar ya está en la base de datos.
    case EMPTY = 3; // Al menos un campo está vacio.
    case NOT_SET = 4; // Al menos un campo no está asignado.
}

// Guarda las variables en un array llamado datos.
$campos = ['email', 'nombre', 'apellido', 'imagenPerfil', 'passwd', 'numeroCelular'];
foreach ($campos as $x) $datos[$x] = $_POST[$x];
$datos['passwd'] = md5($datos['passwd']);
$token = generarToken();
$datos['token'] = md5($token);

// Devuelve por JSON el código de error e inicia sesión si se ha realizado exitosamente el registro.
$error = comprobarError();
if ($error == codigoError::SUCCESS) {
    inicioSesion($datos['email']);
    echo json_encode(['error' => $error, 'token' => $token]);
} else echo json_encode(['error' => $error]);

die();


function comprobarError() {
    global $campos, $datos;

    // Devuelve un código de error si una variable no esta seteada.
    foreach ($campos as $x) if (!isset($_POST[$x])) return codigoError::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($campos as $x) if (empty($_POST[$x])) return codigoError::EMPTY;

    // Devuelve un código de error si el usuario ya esta registrado.
    if (traerPasswd($datos['email']) != null) return codigoError::EXISTENT;

    // Intenta registrar al usuario en la base de datos y devuelve su correspondiente código de error.
    return (nuevoCliente($datos)) ? codigoError::SUCCESS : codigoError::NO_SUCCESS;
}

// Genera un código de 6 caracteres aleatorios.
function generarToken() {
    $bytes = random_bytes(3);
    // Convierte los bytes en una cadena hexadecimal
    $token = bin2hex($bytes);
    return substr($token, 0, 6);
}

// Inicia la sesión por 7 días.
function inicioSesion($email) {
    $_SESSION['user'] = $email;
    ini_set('session.gc_lifetime', 7*24*3600);
}