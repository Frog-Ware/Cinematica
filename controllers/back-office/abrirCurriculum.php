<?php

// Este script abre en una segunda ventana el curriculum elegido para visualizar.

ob_start();
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/insertar.php";
require_once "../../models/db/traer.php";
require_once "../../models/files/subir.php";
require_once "../../models/utilities/validacion.php";
require_once "../../models/utilities/enviarEmail.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case DB_ERR = 1;
    case NOT_FOUND = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::DB_ERR => "Hubo un error en el registro en la BD.",
            self::NOT_FOUND => "El archivo no se encontró.",
            self::VALIDATION => "El nombre del archivo o el ID no pasaron la prueba de validación.",
            self::EMPTY => "Al menos un campo esta vacío.",
            self::NOT_SET => "Al menos un campo no esta seteado.",
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
    // Guarda las variables en un array llamado datos.
    if (isset($_POST['documento']))
        $datos = ['documento' => $_POST['documento'], 'archivo' => $_POST['documento'] . '.pdf'];

    // Devuelve el código de error correspondiente por JSON o devuelve el PDF.
    $error = comprobar($datos);

    if ($error == err::SUCCESS) {
        // Envia un email avisando de que el curriculum ha sido considerado.
        $dest = traerCV($datos['documento']);
        $destN = $dest['nombre'] . ' ' . $dest['apellido'];
        enviarEmail($dest['email'], 'CV Leido', ['nombre' => $destN]);

        // Actualiza el log y limpia el buffer.
        file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

        // Muestra el PDF.
        $dir = '../../views/assets/files/curriculos/' . $datos['archivo'];
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($dir) . '"');
        header('Content-Length: ' . filesize($dir));
        readfile($dir);
    } else {
        // Actualiza el log y limpia el buffer.
        file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

        // Devuelve un JSON con la respuesta.
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(['error' => $error, 'errMsg' => $error->getMsg()]);
    }
}

function comprobar($datos)
{
    // Devuelve un código de error si el nombre del archivo no esta seteado.
    foreach (['archivo', 'documento'] as $x)
        if (!isset($x))
            return err::NOT_SET;

    // Devuelve un código de error si el nombre del archivo esta vacío.
    foreach (['archivo', 'documento'] as $x)
        if (blank($x))
            return err::EMPTY;

    // Devuelve un código de error si el nombre del archivo no pasa la validación.
    if (!validacion($datos))
        return err::VALIDATION;

    // Devuelve un código de error si no encuentra el archivo.
    if (!checkFile($datos['archivo'], 'files/curriculos'))
        return err::NOT_FOUND;

    // Intenta eliminar el artículo de la base de datos y devuelve su correspondiente código de error.
    return actEstadoCV($datos['documento']) ?
        err::SUCCESS : err::DB_ERR;
}

function validacion($datos)
{
    // Valida el nombre del archivo, verificando que solo contenga carácteres permitidos.
    if (!validarStr($datos['archivo'], 20))
        return false;

    // Valida el documento, verificando que solo contenga dígitos.
    if (!validarInt($datos['documento'], 8))
        return false;

    return true;
}