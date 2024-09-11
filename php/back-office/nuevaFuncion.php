<?php

// Este script agrega una función (día, hora y pelicula a proyectar) a la base de datos.

header("Content-Type: application/json; charset=utf-8");
require_once "../db/insertar.php";
require_once "../db/traer.php";
require_once "../utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{ 
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case TAKEN = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;
    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::TAKEN => "La sala está ocupada en ese horario.",
            self::VALIDATION => "Un campo o mas no pasaron la prueba de validación.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Genera una ID para la función.
    $datos['idFuncion'] = generarID();

    // Guarda las variables en un array llamado datos.
    foreach (['idProducto', 'nombreCine', 'numeroSala', 'fechaPelicula', 'horaPelicula', 'dimension'] as $x)
        $datos[$x] = $_POST[$x];

    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar($datos);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];
    echo json_encode($response);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed');
}

// Mata la ejecución.
die();



// Funciones

function comprobar($datos)
{
    // Devuelve un código de error si una variable no esta seteada.
    foreach ($datos as $x)
        if (!isset($x))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($datos as $x)
        if (empty($x))
            return err::EMPTY;

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($datos))
        return err::VALIDATION;

    // Devuelve un código de error si hay una función en un rango de 2hs y 30 min, antes o despues de la función a agregar.
    $funcFecha = traerFuncFecha($datos['fechaPelicula'], 'horaPelicula, nombreCine, numeroSala');
    if (!empty($funcFecha)) {
        foreach ($funcFecha as $x)
        if ($datos['nombreCine'] == $x['nombreCine'] && $datos['numeroSala'] == $x['numeroSala'])
            if (rangoHorario($datos['horaPelicula'], $x['horaPelicula'], 2.5))
                return err::TAKEN;
    }
    
    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (nuevaFunc($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
}

// Genera un ID de 9 numeros aleatorios.
function generarID()
{
    do
        $id = mt_rand(100000000, 999999999);
    while (traerPelicula  ($id, 'idProducto') != null);
    return $id;
}

function validacion($datos)
{
    // Valida ciertos datos, verificando que solo contengan digitos y su longitud este en el rango permitido.
    foreach (['idProducto', 'numeroSala'] as $x)
        if (!validarInt($datos[$x]))
            return false;

    // Valida el nombre del cine, verificando que solo contenga carácteres alfabéticos y que exista.
    if (!validarAl($datos['nombreCine'], 20))
        return false;
    $pos = array_search($datos['nombreCine'], array_column(traerCines(), 'nombreCine'));
    if ($pos === null || !in_array($datos['numeroSala'], traerCines()[$pos]['salas']))
        return false;

    // Valida la fecha, verificando que este en el formato permitido.
    if (!validarFecha($datos['fechaPelicula']))
        return false;

    // Verifica que sea una fecha posterior a hoy.
    $hoy = new DateTime('now', new DateTimeZone('America/Montevideo'));
    if ($datos['fechaPelicula'] < $hoy)
        return false;

    // Valida la hora, verificando que este en el formato permitido.
    if (!validarHora($datos['horaPelicula']))
        return false;

    // Valida que la dimension en que está la película esté en la BD.
    if (!in_array($datos['dimension'], array_column(traerDimensiones(), 'dimension')))
        return false;

    // Si todos los campos estan bien, retorna true.
    return true;
}

// Devuelve si un horario 1 está cerca de un horario 2, siendo hs el margen.
function rangoHorario($h1, $h2, $hs)
{
    return $h1 > date('H:i:s', strtotime($h2) - $hs * 3600) &&
           $h1 < date('H:i:s', strtotime($h2) + $hs * 3600);
}