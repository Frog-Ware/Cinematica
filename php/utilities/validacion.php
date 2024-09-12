<?php

// Verifica que una variable sea vacía (string vacío, nula, solo espacios en blanco o un array vacío).
function blank($var) {
    return is_null($var) || $var === '' || preg_match('/^\s*$/', $var) || (is_array($var) && empty($var));
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
    $regex = '/^(?!\s*$)[a-zA-ZáéíóúÁÉÍÓÚñÑüÜçÇ ,-]+$/';
    return preg_match($regex, $var) && strlen($var) <= $len;
}

// Valida una URL, verificando que el orden haga sentido y su longitud sea la permitida.
function validarURL($var, $len)
{
    $regex = '/^(https?:\/\/)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/\S*)?$/';
    return preg_match($regex, $var) && strlen($var) <= $len;
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