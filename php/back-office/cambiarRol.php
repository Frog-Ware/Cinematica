<?php

// Este script cambia el rol de un cliente a un vendedor o administrador.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case NONEXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NONEXISTENT => "El usuario asociado a ese email no existe",
            self::VALIDATION => "El email no pasó la prueba de validación.",
            self::EMPTY => "El email está vacío.",
            self::NOT_SET => "El email no está seteado."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Guarda las variables en un array llamado datos.
    $datos = [];
    foreach (['email', 'rol'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];

    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar($datos);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}



// Funciones

function comprobar($datos)
{
    // Devuelve un código de error si una variable no esta seteada.
    foreach (['email', 'rol'] as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($datos as $x)
        if (blank($x))
            return err::EMPTY;

    // Devuelve un código de error si el email no pasa la validación.
    if (!validacion($datos))
        return err::VALIDATION;

    // Devuelve un código de error si el usuario no existe o si tiene un rol diferente a cliente.
    if (is_null(traerUsuario($datos['email'])) || traerRol($datos['email']))
        return err::NONEXISTENT;

    // Intenta cambiar el rol al usuario.
    return (cambiarRol($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($datos)
{
    // Valida el email, verificando que solo contenga carácteres permitidos.
    if (!validarEmail($datos['email'], 50))
        return false;

    // Valida el rol, verificando que sea un 0 u 1.
    return $datos['rol'] === 1 || $datos['rol'] === 2;
}