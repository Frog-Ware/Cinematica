<?php

// Este script actualiza los datos de un artículo o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../files/subir.php";
require_once "../utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
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
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NONEXISTENT => "El artículo a actualizar no existe.",
            self::VALIDATION => "Un campo o mas no pasaron la prueba de validación.",
            self::EMPTY => "Todos los campos o el campo ID estan vacios.",
            self::ID_NOT_SET => "La ID no esta seteada.",
            self::IMG_ERR => "La imagen tiene un error."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Guarda las variables en un array llamado datos.
    $datos = filtrar(['nombreArticulo', 'descripcion', 'precio'], $_POST);

    // Verifica que la imagen este correctamente subida.
    $img = $_FILES['imagen']['error'] == UPLOAD_ERR_OK ?
        $_FILES['imagen'] : null;

    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar($datos, $img, $idProducto);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();



// Funciones

function comprobar($datos, $img, $idProducto)
{
    // Devuelve un código de error si el ID no esta seteado.
    if (isset($_POST['idProducto'])) {
        $idProducto = $_POST['idProducto'];
    } else {
        return err::ID_NOT_SET;
    }

    // Devuelve un código de error si el ID o todos los otros campos estan vacios.
    if (blank($idProducto) || (blank($datos) && blank($img)))
        return err::EMPTY;

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($datos, $img, $idProducto))
        return err::VALIDATION;

    // Devuelve un código de error si no existe el artículo a actualizar.
    $articuloDB = traerArticulo($idProducto);
    if (empty($articuloDB))
        return err::NONEXISTENT;

    // Actualiza la imagen o su nombre de ser necesario.
    if (isset($datos['nombreArticulo'])) {
        $datos['imagen'] = str_replace(" ", "_", $datos['nombreArticulo'] . "_imagen.webp");
        $ok = isset($img) ?
            actImg($img, $datos['imagen'], $articuloDB['imagen'], 'articulos') :
            actNombreImg($datos['imagen'], $articuloDB['imagen'], 'articulos');
        if (!$ok)
            return err::IMG_ERR;
    } else if (isset($img))
        if (!actImg($img, $articuloDB['imagen'], $articuloDB['imagen'], 'articulos'))
            return err::IMG_ERR;

    // Intenta ingresar el artículo en la base de datos y devuelve su correspondiente código de error.
    if (!blank($datos))
        return (actArticulo($datos, $idProducto)) ?
            err::SUCCESS : err::NO_SUCCESS;
    else
        return err::SUCCESS;
}

// Devuelve un array con solo los campos solicitados que tienen algun valor.
function filtrar($claves, $arr)
{
    $arrF = [];
    foreach ($claves as $k)
        if (!blank($arr[$k]))
            $arrF[$k] = $arr[$k];
    return $arrF;
}

function validacion($datos, $img, $idProducto)
{
    // Valida el ID, verificando que solo contenga digitos.
    if (!validarInt($idProducto))
        return false;

    // Valida ciertos datos, verificando que solo contengan caracteres permitidos y su longitud este en el rango permitido.
    foreach (['descripcion' => 250, 'nombreArticulo' => 50] as $k => $v)
        if (isset($datos[$k]) && !validarStr($datos[$k], $v))
            return false;

    // Valida el precio, verificando que solo contenga digitos.
    if (isset($datos['precio']) && !validarInt($datos['precio']))
        return false;

    // Valida el tamaño y el tipo de la imagen.
    if (isset($img) && !validarImg($img, 'webp', 200))
        return false;

    // Si todos los campos estan bien, retorna true.
    return true;
}