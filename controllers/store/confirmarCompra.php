<?php

// Este script confirma una compra y devuelve una "factura".

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
    case NO_SESSION = 2;
    case EMPTY = 3;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "Hubo un error en la inserción en la base de datos.",
            self::NO_SESSION => "La sesión no está iniciada.",
            self::EMPTY => "El carrito está vacío."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Devuelve el código de error correspondiente mediante JSON.
    $carritoDB = isset($_SESSION['user']) ?
        traerCarrito($_SESSION['user']) : null;
    $datos['idCompra'] = generarID('traerCompra');
    $error = comprobar($datos);
    $response = ($error == err::SUCCESS) ? 
        ['error' => $error, 'errMsg' => $error->getMsg(), 'datos' => organizar()] :
        ['error' => $error, 'errMsg' => $error->getMsg()];
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
    // Devuelve un código de error si la sesión no está iniciada o si el carrito no existe.
    global $carritoDB;
    if (isset($_SESSION['user'])) {
        $datos['email'] = $_SESSION['user'];
    } else {
        return err::NO_SESSION;
    }

    if (is_null($carritoDB))
        return err::EMPTY;

    // Asigna los datos restantes.
    $datos['idFuncion'] = $carritoDB['idFuncion'];
    $datos['fechaCompra'] = date("Y-m-d");
    $datos['asientos'] = $carritoDB['asientos'];
    $datos['precio'] = obtenerTotal($carritoDB);

    // Intenta persistir el carrito en la base de datos
    return nuevaCompra($datos) && comprarAsientos($datos) && eliminarCarrito($carritoDB['email']) ?
        err::SUCCESS : err::NO_SUCCESS;
}

function obtenerTotal($carritoDB)
{
    $pelicula = traerPrecioD(traerFunc($carritoDB['idFuncion'])['dimension']);
    $precioArt = 0;
    if (isset($carritoDB['articulos']))
        foreach ($carritoDB['articulos'] as $x)
            $precioArt += traerArticulo($x['idProducto'])['precio'] * $x['cantidad'];

    return $pelicula + $precioArt;
}

function organizar()
{
    global $carritoDB;
    $factura['cliente'] = traerUsuario($carritoDB['email'])['nombre'] . " " . traerUsuario($carritoDB['email'])['apellido'];
    $factura['fechaCompra'] = date("Y-m-d");
    $funcion = traerFunc($carritoDB['idFuncion']);
    $factura['pelicula'] = ['nombrePelicula' => traerPelicula($funcion['idProducto'])['nombrePelicula'],
                            'fecha' => $funcion['fechaPelicula'],
                            'hora' => $funcion['horaPelicula'],
                            'cine' => $funcion['nombreCine'],
                            'sala' => $funcion['numeroSala'],
                            'asientos' => $carritoDB['asientos'],
                            'precio' => traerPrecioD($funcion['dimension'])];
    if (isset($carritoDB['articulos'])) {
        foreach ($carritoDB['articulos'] as $x) {
            $art = traerArticulo($x['idProducto']);
            $factura['articulos'][] = ['nombreArticulo' => $art['nombreArticulo'],
                                       'cantidad' => $x['cantidad'],
                                       'precio' => $art['precio'] * $x['cantidad']];
        }
    }
    $factura['precioFinal'] = obtenerTotal($carritoDB);

    return $factura;
}