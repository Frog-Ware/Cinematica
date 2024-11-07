<?php

// Este script elimina un empleado según el email ingresado.

ob_start();
header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/insertar.php";
require_once "../../models/db/traer.php";
require_once "../../models/files/subir.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case DB_ERR = 1;
    case NONEXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::DB_ERR => "Hubo un error en la remoción en la base de datos.",
            self::NONEXISTENT => "El usuario a eliminar no existe.",
            self::VALIDATION => "El email no pasó la prueba de validación.",
            self::EMPTY => "El email está vacío.",
            self::NOT_SET => "El email no está seteado."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    isset($_SESSION['user']) && traerRol($_SESSION['user']) == 2 ?
        main() : header('HTTP/1.1 401 Unauthorized', true, 401);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed', true, 405);
}

exit;



// Funciones


function main()
{
    // Devuelve el código de error correspondiente por JSON.
    $error = comprobar();
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
}

function comprobar()
{
    // Devuelve un código de error si el email no está seteado.
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    } else {
        return err::NOT_SET;
    }

    // Devuelve un código de error si el email esta vacío.
    if (blank($email))
        return err::EMPTY;

    // Devuelve un código de error si el email o la contraseña no pasan la validación.
    if (!validacion($email))
        return err::VALIDATION;

    // Devuelve un código de error si no existe el email a eliminar.
    $passwd = traerPasswd($email);
    if (in_array($email, traerEmpleados(0)))
        return err::NONEXISTENT;

    // Intenta eliminar el cliente de la base de datos y devuelve su correspondiente código de error.
    return (eliminarUsuario($email)) ?
        err::SUCCESS : err::DB_ERR;
}

function validacion($email)
{
    // Valida el email ingresado, verificando que este en el formato permitido y su longitud este en el rango permitido.
    return validarEmail($email, 50);
}