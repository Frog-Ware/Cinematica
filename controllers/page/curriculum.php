<?php

// Este script guarda un curriculum en la base de datos.

ob_start();
header("Content-Type: application/json; charset=utf-8");
require_once "../../models/db/traer.php";
require_once "../../models/db/insertar.php";
require_once "../../models/utilities/validacion.php";
require_once "../../models/files/subir.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case DB_ERR = 1;
    case EXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;
    case FILE_ERR = 6;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::DB_ERR => "Hubo un error al registrarlo en la DB.",
            self::EXISTENT => "Hay un currículum asociado con ese documento en la base de datos.",
            self::VALIDATION => "Uno o mas campos no pasaron la prueba de validación.",
            self::EMPTY => "Uno o mas campos están vacios.",
            self::NOT_SET => "Uno o mas campos no están seteados.",
            self::FILE_ERR => "Hubo un error al subir el archivo."
        };
    }
}

$_SERVER['REQUEST_METHOD'] == 'POST' ?
    main() : header('HTTP/1.0 405 Method Not Allowed');
    
exit;



// Funciones

function main()
{
    // Guarda las variables en un array llamado datos.
    $datos = [];
    foreach (['email', 'nombre', 'apellido', 'documento', 'numeroCelular'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];

    // Verifica los datos y retorna un codigo de error.
    $error = comprobar($datos);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];

    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar($datos)
{
    // Verifica que todos los datos estén seteados.
    foreach (['email', 'nombre', 'apellido', 'documento', 'numeroCelular'] as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;
    if ((isset($_FILES['curriculum']) && $_FILES['curriculum']['error'] == UPLOAD_ERR_OK)) {
        $cvpdf = $_FILES['curriculum'];
    } else {
        return err::NOT_SET;
    }

    // Verifica que ningun dato esté vacío.
    foreach (['email', 'nombre', 'apellido', 'documento', 'numeroCelular'] as $x)
        if (blank($datos[$x]))
            return err::EMPTY;

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($datos, $cvpdf))
        return err::VALIDATION;

    // Devuelve un código de error si hay un curriculum con ese documento en la BD.
    if (existe('documento', 'CV', $datos['documento']))
        return err::EXISTENT;

    // Sube el PDF al servidor.
    $datos['archivo'] = $datos['documento'] . '.pdf';
    if (!subirFile($cvpdf, $datos['archivo'], 'files/curriculos'))
        return err::FILE_ERR;

    // Intenta ingresar el artículo en la base de datos y devuelve su correspondiente código de error.
    return (nuevoCV($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function validacion($datos, $cvpdf)
{
    // Valida el nombre y apellido, verificando que solo contenga carácteres alfabéticos y su longitud este en el rango permitido.
    foreach (['nombre', 'apellido'] as $x)
        if (!validarStr($datos[$x], 20))
            return false;

    // Valida el email ingresado, verificando que este en el formato permitido y su longitud este en el rango permitido.
    if (!validarEmail($datos['email'], 50))
        return false;

    // Valida el numero celular ingresado, verificando que solo contenga dígitos y su longitud este en el rango permitido.
    if (!validarInt($datos['numeroCelular']))
        return false;

    // Valida el documento ingresado, verificando que solo contenga dígitos y su longitud este en el rango permitido.
    if (!validarInt($datos['documento'], 8))
        return false;

    // Valida el tamaño y el tipo de la imagen.
    if (!validarPDF($cvpdf, 'application/pdf', 10240))
        return false;

    return true;
}