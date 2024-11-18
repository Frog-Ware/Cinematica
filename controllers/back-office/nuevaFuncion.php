<?php

// Este script agrega una función (día, hora y pelicula a proyectar) a la base de datos.

ob_start();
header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/insertar.php";
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{ 
    case SUCCESS = 0;
    case NO_SUCCESS = 1;
    case TAKEN = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;
    case NONEXISTENT = 6;
    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::TAKEN => "La sala está ocupada en ese horario.",
            self::VALIDATION => "Un campo o mas no pasaron la prueba de validación.",
            self::EMPTY => "Al menos un campo está vacio.",
            self::NOT_SET => "Al menos un campo no está asignado.",
            self::NONEXISTENT => "La película no existe o no está en cartelera."
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

exit;



// Funciones

function main()
{
    // Genera una ID para la función.
    $datos['idFuncion'] = generarID('traerFunc');

    // Guarda las variables en un array llamado datos.
    foreach (['idProducto', 'nombreCine', 'numeroSala', 'fechaPelicula', 'horaPelicula', 'dimension'] as $x)
        if (isset($_POST[$x]))
            $datos[$x] = $_POST[$x];

    // Devuelve el código de error correspondiente mediante JSON.
    $error = comprobar($datos);
    $response = ['error' => $error, 'errMsg' => $error->getMsg()];

    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar($datos)
{
    // Devuelve un código de error si una variable no esta seteada.
    foreach (['idProducto', 'nombreCine', 'numeroSala', 'fechaPelicula', 'horaPelicula', 'dimension'] as $x)
        if (!isset($datos[$x]))
            return err::NOT_SET;

    // Devuelve un código de error si una variable esta vacía.
    foreach ($datos as $x)
        if (blank($x))
            return err::EMPTY;

    // Devuelve un código de error si algun campo no pasa la validación.
    if (!validacion($datos))
        return err::VALIDATION;

    // Verifica que la película esté en cartelera.
    if (($ids = traerIdCartelera()) && !is_null($ids) && !in_array($datos['idProducto'], array_column($ids, 'idProducto')))
        return err::NONEXISTENT;

    // Devuelve un código de error si hay una función en un rango de 2hs y 30 min, antes o despues de la función a agregar.
    if (($func = traerFuncFecha($datos['fechaPelicula'])) && !is_null($func)) {
        foreach ($func as $x)
        if ($datos['nombreCine'] == $x['nombreCine'] && $datos['numeroSala'] == $x['numeroSala'])
            if (rangoHorario($datos['horaPelicula'], $x['horaPelicula'], 2.5))
                return err::TAKEN;
    }
    
    // Intenta ingresar la película en la base de datos y devuelve su correspondiente código de error.
    return (nuevaFunc($datos)) ?
        err::SUCCESS : err::NO_SUCCESS;
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
    if (is_null($pos) || !in_array($datos['numeroSala'], array_column(traerCines()[$pos]['salas'], 'numeroSala')))
        return false;

    // Valida la fecha, verificando que este en el formato permitido.
    if (!validarFecha($datos['fechaPelicula']))
        return false;

    // Verifica que sea una fecha posterior a hoy.
    $hoy = new DateTime('now', new DateTimeZone('America/Montevideo'));
    if (new DateTime($datos['fechaPelicula'], new DateTimeZone('America/Montevideo'))< $hoy)
        return false;

    // Valida la hora, verificando que este en el formato permitido.
    $datos['horaPelicula'] .= ":00";
    if (!validarHora($datos['horaPelicula']))
        return false;

    // Valida que la dimension en que está la película esté en la BD.
    if (!in_array($datos['dimension'], traerDimensiones()))
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