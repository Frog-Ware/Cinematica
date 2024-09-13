<?php

// Este script actualiza los datos de una película o devuelve un código de error según la coincidencia de los valores ingresados por el usuario y los valores guardados en la base de datos.

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
            self::NONEXISTENT => "La película a actualizar no existe.",
            self::VALIDATION => "Un campo o mas no pasaron la prueba de validación.",
            self::EMPTY => "Todos los campos o el campo ID estan vacios.",
            self::ID_NOT_SET => "La ID no esta seteada.",
            self::IMG_ERR => "Al menos una imagen tiene un error."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Guarda las variables en un array llamado datos y las variables múltiples en un array bidimensional llamado datosArr.
    $datos = filtrar(['actores', 'sinopsis', 'duracion', 'nombrePelicula', 'pegi', 'trailer', 'director'], $_POST, false);
    $datosArr = filtrar(['categorias', 'dimensiones', 'idiomas'], $_POST, true);

    // Verifica que las imagenes esten correctamente subidas.
    $img = [];
    foreach (['poster', 'cabecera'] as $x)
        (isset($_FILES[$x]) && $_FILES[$x]['error'] == UPLOAD_ERR_OK) ?
            $img[$x] = $_FILES[$x] : null;

    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar($datos, $datosArr, $img);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();



// Funciones

function comprobar($datos, $datosArr, $img)
{
    // Devuelve un código de error si el ID no esta seteado.
    if (isset($_POST['idProducto'])) {
        $idProducto = $_POST['idProducto'];
    } else {
        return err::ID_NOT_SET;
    }
    
    // Devuelve un código de error si el ID o todos los otros campos estan vacios.
    if (blank($idProducto) || (blank($datos) && blank($datosArr) && blank($img)))
        return err::EMPTY;

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($datos, $datosArr, $img, $idProducto))
        return err::VALIDATION;

    // Devuelve un código de error si no existe la pelicula a actualizar.
    $peliculaDB = traerPelicula($idProducto);
    if ($peliculaDB == null)
        return err::NONEXISTENT;

    // Actualiza la imagen o su nombre de ser necesario.
    if (isset($datos['nombrePelicula'])) {
        foreach (['poster', 'cabecera'] as $x) {
            $datos[$x] = str_replace(" ", "_", $datos['nombrePelicula'] . "_$x.webp");
            $ok = isset($img[$x]) ?
                actImg($img[$x], $datos[$x], $peliculaDB[$x], 'peliculas') :
                actNombreImg($datos[$x], $peliculaDB[$x], 'peliculas');
            if (!$ok)
                return err::IMG_ERR;
        }
    } else if (!blank($img)) {
        foreach (['poster', 'cabecera'] as $x)
            if (isset($img[$x]) && !actImg($img[$x], $peliculaDB[$x], $peliculaDB[$x], 'peliculas'))
                return err::IMG_ERR;
    }

    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (actPelicula($datos, $datosArr, $idProducto)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

// Devuelve un array con solo los campos solicitados que tienen algun valor.
function filtrar($claves, $arr, $explode)
{
    $arrF = [];
    foreach ($claves as $k)
        if (!blank($arr[$k]))
            $arrF[$k] = $explode ?
                explode(', ', $arr[$k]) : $arr[$k];
    return !blank($arrF) ?
        $arrF : null;
}

function validacion($datos, $datosArr, $img, $idProducto)
{   
    // Valida el ID, verificando que solo contenga digitos.
    if (!validarInt($idProducto))
        return false;

    // Valida ciertos datos, verificando que solo contengan caracteres permitidos y su longitud este en el rango permitido.
    foreach (['sinopsis' => 250, 'nombrePelicula' => 50, 'pegi' => 10] as $k => $v)
        if (isset($datos[$k]) && !validarStr($datos[$k], $v))
            return false;

    // Valida otros datos, verificando que solo contengan caracteres alfabéticos y su longitud este en el rango permitido.
    foreach (['actores' => 250, 'director' => 50] as $k => $v)
        if (isset($datos[$k]) && !validarAl($datos[$k], $v))
            return false;

    // Valida la duracion, verificando que este en el formato permitido.
    if (isset($datos['duracion']) && !validarHora($datos['duracion']))
        return false;

    // Valida la URL del trailer.
    if (isset($datos['trailer']) && !validarURL($datos['trailer'], 250))
        return false;

    // Valida el tamaño y el tipo de las imágenes.
    foreach ($img as $x)
        if (!blank($x) && !validarImg($x, 'webp', 200))
            return false;

    // Valida los datos múltiples, verificando que existan.
    if (isset($datosArr))
        foreach ($datosArr as $k => $v)
            foreach ($v as $x) {
                $func = "traer$k";
                if (!in_array($x, array_column($func(), array_key_first($func()[0]))))
                    return false;
            }

    // Si todos los campos estan bien, retorna true.
    return true;
}