<?php

// Elimina un artículo del carrito.

ob_start();
header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/traer.php";
require_once "../../models/db/insertar.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case NONEXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case ID_NOT_SET = 5;
    case NO_SESSION = 6;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la remoción en la base de datos.",
            self::NONEXISTENT => "El artículo no está en el carrito.",
            self::VALIDATION => "El ID del artículo no pasó la validación.",
            self::EMPTY => "El ID del artículo está vacío.",
            self::ID_NOT_SET => "El ID del artículo no está seteado.",
            self::NO_SESSION => "La sesión no está iniciada."
        };
    }
}

// Restringe el acceso si no se utiliza el método de solicitud adecuado.
$_SERVER['REQUEST_METHOD'] == 'POST' ? 
    main() : header('HTTP/1.0 405 Method Not Allowed');

exit;



// Funciones

function main()
{
    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar();
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];

    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    echo json_encode($response);
}

function comprobar()
{
    // Devuelve un código de error si la sesión no está iniciada.
    if (isset($_SESSION['user'])) {
        $email = $_SESSION['user'];
    } else {
        return err::NO_SESSION;
    }

    // Devuelve un código de error si el ID no está seteado.
    if (isset($_POST['idProducto'])) {
        $idProducto = $_POST['idProducto'];
    } else {
        return err::ID_NOT_SET;
    }

    // Devuelve un código de error si el ID está vacío.
    if (blank($idProducto))
        return err::EMPTY;

    // Devuelve un código de error si el ID no pasa la validación.
    if (!validacion($idProducto))
        return err::VALIDATION;

    // Devuelve un código de error si no existe el artículo a eliminar.
    $carritoDB = traerCarrito($email);
    if (!in_array($idProducto, array_column($carritoDB['articulos'], 'idProducto')))
        return err::NONEXISTENT;

    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return eliminarCarritoArt([$email, $idProducto]) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($idProducto)
{
    return validarInt($idProducto);
}