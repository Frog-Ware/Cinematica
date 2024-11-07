<?php

// Este script confirma una compra y devuelve una "factura".

ob_start();
header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/insertar.php";
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";
require_once "../../models/files/genCompraPDF.php";

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

// Verifica que el método utilizado sea POST.
$_SERVER['REQUEST_METHOD'] == 'POST' ?
    main() : header('HTTP/1.0 405 Method Not Allowed', true, 405);

exit;



// Funciones

function main()
{
    // Devuelve el código de error correspondiente mediante JSON.
    $datos['idCompra'] = generarID('traerCompra');
    $error = comprobar($datos);
    $response = ($error == err::SUCCESS) ? 
        ['error' => $error, 'errMsg' => $error->getMsg(), 'datos' => organizar($datos['idCompra'])] :
        ['error' => $error, 'errMsg' => $error->getMsg()];

    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    echo json_encode($response);
}

function comprobar($datos)
{
    // Devuelve un código de error si la sesión no está iniciada o si el carrito no existe.
    if (isset($_SESSION['user'])) {
        $datos['email'] = $_SESSION['user'];
        $carritoDB = traerCarrito($_SESSION['user']);
    } else {
        return err::NO_SESSION;
    }

    if (is_null($carritoDB) || ((is_null($carritoDB['idFuncion'])) && (!isset($carritoDB['articulos']))))
        return err::EMPTY;

    // Asigna los datos restantes.
    $datos['fechaCompra'] = date("Y-m-d");
    $datos['precio'] = obtenerTotal($carritoDB);
    if (isset($carritoDB['idFuncion'])) {
        $datos['idFuncion'] = $carritoDB['idFuncion'];
        $datos['asientos'] = $carritoDB['asientos'];
    }
    // Intenta confirmar la compra.
    if (!(nuevaCompra($datos) && eliminarCarrito($carritoDB['email'])))
        return err::NO_SUCCESS;
    if (isset($carritoDB['articulos']) && !nuevaCompraArt($datos['idCompra'], $carritoDB['articulos']))
        return err::NO_SUCCESS;
    if (isset($carritoDB['idFuncion']) && !comprarAsientos($datos))
        return err::NO_SUCCESS;

    return err::SUCCESS;
        
}

function obtenerTotal($carritoDB)
{
    $precioEnt = 0;
    $precioArt = 0;

    // Si hay entradas, suma su precio
    if (isset($carritoDB['idFuncion'])) {
        $cantEntradas = count(explode(', ', $carritoDB['asientos']));
        $precio = traerPrecioD(traerFunc($carritoDB['idFuncion'])['dimension']);
        $precioEnt = $cantEntradas * $precio;
    }
    
    // Si hay artículos, suma su precio.
    if (isset($carritoDB['articulos']))
        foreach ($carritoDB['articulos'] as $x)
            $precioArt += traerArticulo($x['idProducto'])['precio'] * $x['cantidad'];
    
    return $precioEnt + $precioArt;
}

function organizar($id)
{
    global $carritoDB;
    $factura['idCompra'] = $id;
    $factura['cliente'] = traerUsuario($carritoDB['email'])['nombre'] . " " . traerUsuario($carritoDB['email'])['apellido'];
    $factura['fechaCompra'] = date("Y-m-d");
    if (isset($carritoDB['idFuncion'])) {
    $funcion = traerFunc($carritoDB['idFuncion']);
    $factura['pelicula'] = ['nombrePelicula' => traerPelicula($funcion['idProducto'])['nombrePelicula'],
                            'fecha' => $funcion['fechaPelicula'],
                            'hora' => $funcion['horaPelicula'],
                            'cine' => $funcion['nombreCine'],
                            'sala' => $funcion['numeroSala'],
                            'asientos' => $carritoDB['asientos'],
                            'precio' => traerPrecioD($funcion['dimension']) * count(explode(', ', $carritoDB['asientos']))];
    }
    if (isset($carritoDB['articulos'])) {
        foreach ($carritoDB['articulos'] as $x) {
            $art = traerArticulo($x['idProducto']);
            $factura['articulos'][] = ['nombreArticulo' => $art['nombreArticulo'],
                                       'cantidad' => $x['cantidad'],
                                       'precio' => $art['precio']];
        }
    }
    $factura['precioFinal'] = obtenerTotal($carritoDB);
    genPDF($factura);

    return $factura;
}