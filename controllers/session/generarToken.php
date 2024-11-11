<?php

// Genera un token para recuperar su contraseña, el cual tiene una hora de duración.

ob_start();
header("Content-Type: application/json; charset=utf-8");
require_once "../../models/db/traer.php";
require_once "../../models/db/insertar.php";
require_once "../../models/utilities/validacion.php";
require_once "../../models/utilities/enviarEmail.php";

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
            self::NO_SUCCESS => "Hubo un error al registrarlo en la base de datos.",
            self::NONEXISTENT => "No existe una cuenta registrada con ese email.",
            self::VALIDATION => "El email no pasó la validación.",
            self::EMPTY => "El email está vacio.",
            self::NOT_SET => "El email no está asignado."
        };
    }
}

$_SERVER['REQUEST_METHOD'] == 'POST' ?
    main() : header('HTTP/1.0 405 Method Not Allowed');

exit;



// Funciones

function main()
{
    // Verifica que no haya errores en el proceso.
    $token = generarToken();
    $error = comprobar($token);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    if ($error == err::SUCCESS)
        enviarEmail($_POST['email'], 'Token', ['nombre' => traerUsuario($_POST['email'])['nombre'], 'token' => $token]);
    
    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar($token)
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

    // Devuelve un código de error si el email no pasa la validación.
    if (!validacion($email))
        return err::VALIDATION;

    // Verifica que el email esté registrado.
    if (!traerUsuario($email))
        return err::NONEXISTENT;

    // Intenta añadir el token a la base de datos y devuelve su correspondiente código de error.
    return (nuevoToken(md5($token), $email)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($email)
{
    // Valida el email ingresado, verificando que este en el formato permitido y su longitud este en el rango permitido.
    return validarEmail($email, 50);
}

// Genera un código de 6 caracteres aleatorios.
function generarToken()
{
    $bytes = random_bytes(3);
    // Convierte los bytes en una cadena hexadecimal
    $token = bin2hex($bytes);
    return substr($token, 0, 6);
}