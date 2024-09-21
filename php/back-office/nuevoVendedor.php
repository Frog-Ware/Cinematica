<?php

// Este script cambia el rol de un cliente a un vendedor.

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
    case ID_NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NONEXISTENT => "El usuario asociado a ese email no existe",
            self::VALIDATION => "El email no pasó la prueba de validación.",
            self::EMPTY => "El email está vacío.",
            self::ID_NOT_SET => "El email no está seteado."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar();
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}



// Funciones

function comprobar()
{
    // Devuelve un código de error si el email no esta seteado.
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    } else {
        return err::ID_NOT_SET;
    }

    // Devuelve un código de error si el email esta vacío.
    if (blank($email))
        return err::EMPTY;

    // Devuelve un código de error si el email no pasa la validación.
    if (!validacion($email))
        return err::VALIDATION;

    // Devuelve un código de error si el usuario no existe o si tiene un rol diferente a cliente.
    if (is_null(traerUsuario($email)) || traerRol($email))
        return err::NONEXISTENT;

    // Intenta cambiar el rol al usuario.
    return (cambiarRol($email)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($email)
{
    // Valida el email, verificando que solo contenga carácteres permitidos.
    return validarEmail($email, 50);
}