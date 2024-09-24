<?php

// Este script registra un nuevo usuario o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case EXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;
    case IMG_ERR = 6;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::EXISTENT => "El email a registrar ya está en la base de datos.",
            self::VALIDATION => "El input no pasó la validación.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado.",
            self::IMG_ERR => "No se encontró una imagen que coincidiera con el nombre ingresado."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Guarda las variables en un array llamado datos.
    $datos = [];
    foreach (['email', 'nombre', 'apellido', 'imagenPerfil', 'passwd', 'numeroCelular'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];

    // Verifica los datos y registra a un nuevo usuario. Devuelve el código de error correspondiente por JSON.
    $token = generarToken();
    $error = comprobar($datos, $token);
    $response = ($error == err::SUCCESS) ?
        ['error' => $error, 'errMsg' => $error->getMsg(), 'datos' => traerUsuario($datos['email']), 'token' => $token] :
        ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();



// Funciones

function comprobar($datos, $token)
{
    // Devuelve un código de error si una variable no esta seteada.
    foreach (['email', 'nombre', 'apellido', 'imagenPerfil', 'passwd', 'numeroCelular'] as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($datos as $x)
        if (blank($x))
            return err::EMPTY;

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($datos))
        return err::VALIDATION;

    // Devuelve un código de error si la imagen no existe.
    if (!file_exists("../../assets/img/perfil/" . $datos['imagenPerfil']))
        return err::IMG_ERR;

    // Devuelve un código de error si el usuario ya esta registrado.
    if (!is_null(traerPasswd($datos['email'])))
        return err::EXISTENT;

    // Cifra la contraseña y el token generado en md5.
    if (isset($datos['passwd']))
        $datos['passwd'] = md5($datos['passwd']);
    $datos['token'] = md5($token);

    // Intenta registrar al usuario en la base de datos y devuelve su correspondiente código de error.
    return (nuevoCliente($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

// Genera un código de 6 caracteres aleatorios.
function generarToken()
{
    $bytes = random_bytes(3);
    // Convierte los bytes en una cadena hexadecimal
    $token = bin2hex($bytes);
    return substr($token, 0, 6);
}

function validacion($datos)
{
    // Valida el nombre y apellido, verificando que solo contenga carácteres alfabéticos y su longitud este en el rango permitido.
    foreach (['nombre', 'apellido'] as $x)
        if (!validarStr($datos[$x], 20))
            return false;

    // Valida la contraseña, verificando que solo contenga caracteres permitidos y su longitud este en el rango permitido.
    if (!validarStr($datos['passwd'], 12))
        return false;

    // Valida el email ingresado, verificando que este en el formato permitido y su longitud este en el rango permitido.
    if (!validarEmail($datos['email'], 50))
        return false;

    // Valida el numero celular ingresado, verificando que solo contenga dígitos y su longitud este en el rango permitido.
    if (!validarInt($datos['numeroCelular']))
        return false;

    // Si todos los campos estan bien, retorna true.
    return true;
}