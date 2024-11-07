<?php

// Este script cambia el rol de un cliente a un vendedor o administrador.

ob_start();
header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/insertar.php";
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case NOT_VALID = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NOT_VALID => "El usuario asociado a ese email no es válido para un cambio de rol.",
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
    // Guarda las variables en un array llamado datos.
    foreach (['email', 'rol'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];

    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar($datos);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    
    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar($datos)
{
    // Devuelve un código de error si una variable no esta seteada.
    foreach (['email', 'rol'] as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach (['email', 'rol'] as $x)
        if (blank($datos[$x]))
            return err::EMPTY;

    // Devuelve un código de error si el email no pasa la validación.
    if (!validacion($datos))
        return err::VALIDATION;

    // Devuelve un código de error si el usuario no existe o si tiene un rol de administrador.
    if (is_null(traerUsuario($datos['email'])) || traerRol($datos['email']) == 2)
        return err::NOT_VALID;

    // Intenta cambiar el rol al usuario.
    return (cambiarRol($datos, traerRol($datos['email']))) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($datos)
{
    // Valida el email, verificando que solo contenga carácteres permitidos.
    if (!validarEmail($datos['email'], 50))
        return false;

    // Valida el rol, verificando que sea un 0(Cliente), 1(Vendedor) o 2(Administrador).
    return $datos['rol'] <= 2;
}