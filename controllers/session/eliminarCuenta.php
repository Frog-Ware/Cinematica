<?php

// Este script elimina una cuenta según el email ingresado.

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
    case NO_MATCH = 2;
    case NONEXISTENT = 3;
    case VALIDATION = 4;
    case EMPTY = 5;
    case ID_NOT_SET = 6;
    case NO_SESSION = 7;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::DB_ERR => "Hubo un error en la remoción en la base de datos.",
            self::NONEXISTENT => "El usuario a eliminar no existe.",
            self::VALIDATION => "La contraseña no pasó la prueba de validación.",
            self::EMPTY => "La contraseña está vacío.",
            self::ID_NOT_SET => "La contraseña no está seteado.",
            self::NO_SESSION => "La sesión no está iniciada."
        };
    }
}

// Verifica el método utilizado y envia un error 405 de no ser el permitido.
$_SERVER['REQUEST_METHOD'] == 'POST' ?
    main() : header('HTTP/1.0 405 Method Not Allowed', true, 405);

exit;



// Funciones


function main()
{
    // Devuelve el código de error correspondiente por JSON.
    $error = comprobar();
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    
    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar()
{
    // Devuelve un código de error si la sesión no está iniciada.
    if (isset($_SESSION['user']))
        $datos['email'] = $_SESSION['user'];
    else
        return err::NO_SESSION;
        
    // Devuelve un código de error si la contraseña no esta seteada.
    if (isset($_POST['passwd'])) {
        $datos['passwd'] = $_POST['passwd'];
    } else {
        return err::ID_NOT_SET;
    }

    // Devuelve un código de error si la contraseña esta vacía.
    if (blank($datos['passwd']))
        return err::EMPTY;

    // Devuelve un código de error si el email o la contraseña no pasan la validación.
    if (!validacion($datos))
        return err::VALIDATION;

    // Devuelve un código de error si no existe el email a eliminar.
    $passwd = traerPasswd($datos['email']);
    if (is_null($passwd) || traerRol($datos['email']) != 0)
        return err::NONEXISTENT;

    // Devuelve un codigo de error si la contraseña no coincide.
    if (md5($datos['passwd']) != $passwd)
        return err::NO_MATCH;

    // Intenta eliminar el carrito de tenerlo.
    if (!is_null(traerCarrito($datos['email'])) && !eliminarCarrito($datos['email']))
        return err::DB_ERR;

    // Intenta eliminar el cliente de la base de datos y devuelve su correspondiente código de error.
    return (eliminarUsuario($datos['email']) && session_destroy()) ?
        err::SUCCESS : err::DB_ERR;
}

function validacion($datos)
{
    // Valida la contraseña, verificando que solo contenga caracteres permitidos y su longitud este en el rango permitido.
    if (!validarStr($datos['passwd'], 12))
        return false;

    // Valida el email ingresado, verificando que este en el formato permitido y su longitud este en el rango permitido.
    if (!validarEmail($datos['email'], 50))
        return false;

    // Si todos los campos estan bien, retorna true.
    return true;
}