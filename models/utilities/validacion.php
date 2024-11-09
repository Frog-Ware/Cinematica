<?php

require_once "../../models/db/traer.php";

// Genera un ID de 9 numeros aleatorios.
function generarID($func)
{
    do
        $id = mt_rand(100000000, 999999999);
    while (!is_null($func($id)));
    return $id;
}

// Verifica que una variable sea vacía (string vacío, nula, solo espacios en blanco o un array vacío).
function blank($var) 
{
    return is_array($var) ?
        empty($var) : is_null($var) || $var === '' || preg_match('/^\s*$/', $var);
}

function array_blank($arr)
{
    foreach ($arr as $x)
        if (blank($x))
            return true;
    return false;
}

// Devuelve un array solo con las claves elegidas.
function array_pick($arr, $keys)
{
    foreach ([$arr, $keys] as $x)
        if (!is_array($x) || blank($x))
            return null;
    return array_intersect_key($arr, array_flip($keys));
}

// Valida un integer, verificando que solo contenga numeros y su longitud sea la permitida.
function validarInt($var, $len = 9)
{
    $regex = '/^(?!\s*$)\d+$/';
    return preg_match($regex, $var) && strlen($var) <= $len;
}

// Valida un string, verificando que solo contenga carácteres alfanuméricos y otros permitidos, además de que su longitud sea la permitida.
function validarStr($var, $len)
{
    $regex = '/^(?!\s*$)[a-zA-ZáéíóúÁÉÍÓÚñÑüÜçÇ0-9 ,.:¿?()&-]+$/';
    return preg_match($regex, $var) && strlen($var) <= $len;
}

// Valida una variable alfabética, verificando que solo contenga carácteres alfabéticos y su longitud sea la permitida.
function validarAl($var, $len)
{
    $regex = '/^(?!\s*$)[a-zA-ZáéíóúÁÉÍÓÚñÑüÜçÇ ,-._]+$/';
    return preg_match($regex, $var) && strlen($var) <= $len;
}

// Valida una URL, verificando que el orden haga sentido y su longitud sea la permitida.
function validarURL($var, $len)
{
    $regex = '/^(https?:\/\/)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/\S*)?$/';
    return preg_match($regex, subject: $var) && strlen($var) <= $len;
}

function validarEmail($var, $len)
{
    $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    return preg_match($regex, $var) && strlen($var) <= $len;
}

// Valida una imagen, verificando que su formato y tamaño sean los permitidos.
function validarImg($var, $type, $size)
{
    return $var['type'] == "image/$type" && $var['size'] <= $size * 1024;
}

// Valida que la hora esté en el formato adecuado.
function validarHora($var)
{
    $regex = '/^(?:2[0-3]|[01][0-9]|[0-9]):[0-5][0-9]:[0-5][0-9]$/';
    return preg_match($regex, $var);
}

function validarFecha($var)
{
    $regex = '/^20\d\d-(0[13578]|1[02])-(0[1-9]|[12]\d|3[01])$|^20\d\d-(0[469]|11)-(0[1-9]|[12]\d|30)$|^20\d\d-02-(0[1-9]|1\d|2[0-8])$|^(20(?:[02468][048]|[13579][26]))-02-29$/';
    return preg_match($regex, $var);
}

function validarAsientos($var)
{
    $regex = '/^\d{1,2}-\d{1,2}(, \d{1,2}-\d{1,2})*$/';
    return preg_match($regex, $var);
}

function http_err($err) 
{
    if (in_array($err, [401, 404, 405])) {
        http_response_code($err);
        header("Location: /error-$err.php");
    } else {
        http_response_code();
        header("Location: /error-$err.php");
    }
}

function crearLog($log, $file)
{
    $fecha = new DateTime('now', new DateTimeZone('America/Montevideo'));
    $encab = "$file, " . $fecha->format('Y-m-d h:i:s') . ":\n";
    return blank($log) ?
        $encab . "Ejecutado con éxito. \n \n" : "$encab$log \n \n";
}