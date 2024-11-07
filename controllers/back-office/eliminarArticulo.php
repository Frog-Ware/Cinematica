<?php

// Este script elimina un artículo según el ID ingresado.

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
    case ID_NOT_SET = 5;
    case IMG_ERR = 6;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::DB_ERR => "Hubo un error en la remoción en la base de datos.",
            self::NONEXISTENT => "El artículo a eliminar no existe.",
            self::VALIDATION => "El ID no pasó la prueba de validación.",
            self::EMPTY => "El ID esta vacío.",
            self::ID_NOT_SET => "El ID no esta seteado.",
            self::IMG_ERR => "Hubo un error al eliminar la imagen."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    isset($_SESSION['user']) && traerRol($_SESSION['user']) != 0 ?
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

    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar()
{
    // Devuelve un código de error si el ID no esta seteado.
    if (isset($_POST['idProducto'])) {
        $idProducto = $_POST['idProducto'];
    } else {
        return err::ID_NOT_SET;
    }

    // Devuelve un código de error si el ID esta vacío.
    if (blank($idProducto))
        return err::EMPTY;

    // Devuelve un código de error si el ID no pasa la validación.
    if (!validacion($idProducto))
        return err::VALIDATION;

    // Devuelve un código de error si no existe el artículo a eliminar.
    $articuloDB = traerArticulo($idProducto);
    if (is_null($articuloDB))
        return err::NONEXISTENT;

    // Intenta borrar la imagen de la carpeta.
    if (!borrarImg($articuloDB['imagen'], 'articulos'))
        return err::IMG_ERR;

    // Intenta eliminar el artículo de la base de datos y devuelve su correspondiente código de error.
    return (eliminarArticulo($idProducto)) ?
        err::SUCCESS : err::DB_ERR;
}

function validacion($idProducto)
{
    // Valida el ID, verificando que solo contenga digitos.
    return validarInt($idProducto);
}