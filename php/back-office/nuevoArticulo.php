<?php

// Este script registra una nueva película o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

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
    case EXISTENT = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;
    case IMG_ERR = 6;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::EXISTENT => "El producto a añadir ya existe.",
            self::VALIDATION => "Un campo o mas no pasaron la prueba de validación.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado.",
            self::IMG_ERR => "Al menos una imagen tiene un error."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Genera una ID para el producto.
    $datos['idProducto'] = generarID();

    // Guarda las variables en un array llamado datos.
    foreach (['nombreArticulo', 'descripcion', 'precio'] as $x)
        $datos[$x] = $_POST[$x];

    // Verifica que la imagen este correctamente subida.
    $img = $_FILES['imagen']['error'] == UPLOAD_ERR_OK ?
        $_FILES['imagen'] : null;

    // Devuelve el código de error correspondiente.
    $error = comprobar($datos, $img);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();



// Funciones

function comprobar($datos, $img)
{
    // Devuelve un código de error si una variable no esta seteada.
    foreach ($datos as $x)
        if (!isset($x))
            return err::NOT_SET;
    if (!isset($img))
        return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($datos as $x)
        if (blank($x))
            return err::EMPTY;
    if (blank($img))
        return err::EMPTY;

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($datos, $img))
        return err::VALIDATION;

    // Devuelve un código de error si hay un artículo con el mismo nombre.
    if (!empty(traerArticuloNombre($datos['nombreArticulo'])))
        return err::EXISTENT;

    // Guarda el nombre de la imagen en datos.
    $datos['imagen'] = str_replace(" ", "_", $datos['nombreArticulo'] . "_imagen.webp");

    // Intenta subir la imagen a la carpeta.
    if (!subirImg($img, $datos['imagen'], 'articulos'))
        return err::IMG_ERR;

    // Intenta ingresar el artículo en la base de datos y devuelve su correspondiente código de error.
    return (nuevoArticulo($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

// Genera un ID de 9 numeros aleatorios.
function generarID()
{
    do
        $id = mt_rand(100000000, 999999999);
    while (!is_null(traerArticulo($id)));
    return $id;
}

function validacion($datos, $img) {
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