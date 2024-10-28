<?php

// Este script registra una nueva imagen para el slider/expositor.

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
    case NO_SUCCESS = 1;
    case VALIDATION = 2;
    case EMPTY = 3;
    case NOT_SET = 4;
    case IMG_ERR = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::VALIDATION => "La imagen o su nombre no pasaron la prueba de validación.",
            self::EMPTY => "Un campo está vacio.",
            self::NOT_SET => "Un campo no está asignado.",
            self::IMG_ERR => "La imagen tiene un error."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    isset($_SESSION['user']) && traerRol($_SESSION['user']) != 0 ?
        main() : header('HTTP/1.1 401 Unauthorized', true, 401);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();



// Funciones

function main()
{
    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar();
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
}

function comprobar()
{
    if (isset($_POST['nombre']) && $_FILES['imagen'] && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $nmb = str_replace(' ', '_', $_POST['nombre']) . ".webp";
        $img = $_FILES['imagen'];
    } else {
        return err::NOT_SET;
    }

    if (blank($nmb) || blank($img))
        return err::EMPTY;

    if (!validacion($nmb, $img))
        return err::VALIDATION;
    
    if (!subirImg($img, $nmb, 'slider'))
        return err::IMG_ERR;

    // Intenta ingresar el nombre de la imagen en la base de datos y devuelve su correspondiente código de error.
    return (nuevoSlider($nmb)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($nmb, $img)
{
    // Valida que el nombre de la imagen tenga los caracteres permitidos.
    if (!validarStr($nmb, 40))
        return false;

    // Verifica que el nombre de la imagen no exista.
    if (!is_null(traerSlider()) && in_array($nmb, traerSlider()))
        return false;

    // Valida que la imagen sea del tipo y tamaño permitidos.    
    if (!validarImg($img, 'webp', 500))
        return false;

    return true;
}