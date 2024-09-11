<?php

// Este script elimina el carrito asociado con el email enviado.

header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../db/traer.php";
require_once "../db/insertar.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case NONEXISTENT = 2;
    case NO_SESSION = 3;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la remoción en la base de datos.",
            self::NONEXISTENT => "El carrito no existe.",
            self::NO_SESSION => "La sesión no está iniciada."
        };
    }
}

// Devuelve el código de error correspondiente.
$error = comprobarError();
$response = ['error' => $error, 'errMsg' => $error->getMsg()];

// Envía los datos mediante JSON.
echo json_encode($response);

// Mata la ejecución.
die();



// Funciones

function comprobarError()
{
    // Devuelve un código de error si la sesión no está iniciada.
    if (isset($_SESSION['user']))
        $email = $_SESSION['user'];
    else
        return err::NO_SESSION;

    // Devuelve un código de error si no existe el artículo a eliminar.
    $carritoDB = traerCarrito($email);
    if ($carritoDB == null)
        return err::NONEXISTENT;

    // Guarda los asientos eliminados en una variable.
    $asientos = explode(', ', traerAsientos($carritoDB['idFuncion']));
    $aEliminar = explode(', ', $carritoDB['asientos']);
    $actAsientos = implode(', ', array_diff($asientos, $aEliminar));

    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (eliminarAsientos($carritoDB['idFuncion'], $actAsientos) && eliminarCarrito($email)) ?
        err::SUCCESS : err::NO_SUCCESS;
}