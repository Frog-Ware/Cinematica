<?php

// Este script confirma una compra y devuelve una "factura".

ob_start();
header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/insertar.php";
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";
require_once "../../models/utilities/enviarEmail.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case DB_ERR = 1;
    case NO_MATCH = 2;
    case VALIDATION = 3;
    case EMPTY = 4;
    case NOT_SET = 5;
    case NO_SESSION = 6;
    case NO_INSTANCE = 7;
    case NO_CC = 8;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::DB_ERR => "Hubo un error en la inserción en la base de datos.",
            self::NO_MATCH => "La contraseña no coincidió.",
            self::VALIDATION => "La contraseña o la tarjeta de crédito no ha pasado la validación",
            self::EMPTY => "Uno de los campos está vacío",
            self::NOT_SET => "Uno de los campos no está seteado",
            self::NO_SESSION => "La sesión no está iniciada.",
            self::NO_INSTANCE => "El carrito está vacío o no esta seteado.",
            self::NO_CC => "No hay una tarjeta de crédito en la base de datos."
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
    global $carritoDB;
    $datos['idCompra'] = generarID('traerCompra');
    $error = comprobar($datos);
    $response = ($error == err::SUCCESS) ? 
        ['error' => $error, 'errMsg' => $error->getMsg(), 'datos' => organizar($datos['idCompra'])] :
        ['error' => $error, 'errMsg' => $error->getMsg()];

    // Envia un email con la confirmación y factura de la compra.
    if ($error == err::SUCCESS)
        enviarEmail($response['datos']['email'], 'Compra', $response['datos']);

    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($response);
}

function comprobar($datos)
{
    // Devuelve un código de error si la sesión no está iniciada o si el carrito no existe.
    if (isset($_SESSION['user'])) {
        $datos['email'] = $_SESSION['user'];
    } else {
        return err::NO_SESSION;
    }

    // Verifica que el carrito no esté vacío.
    global $carritoDB;
    $carritoDB = traerCarrito($_SESSION['user']);
    if (is_null($carritoDB) 
        or ((is_null($carritoDB['idFuncion'])) && (!isset($carritoDB['articulos']))))
            return err::NO_INSTANCE;

    // Verifica que si la tarjeta no está seteada, haya una en la base de datos.
    if (isset($_POST['numeroTarjeta'], $_POST['banco'])) {
        foreach (['numeroTarjeta', 'banco'] as $x)
            $validDatos[$x] = $_POST[$x];
    } else if (is_null(traerCC($datos['email']))) {
        return err::NO_CC;
    }

    // Verifica si la contraseña está seteada.
    if (isset($_POST['passwd'])) {
        $validDatos['passwd'] = $_POST['passwd'];
    } else {
        return err::NOT_SET;
    }

    // Verifica que los datos no estén vacíos.
    if ((isset($validDatos['numeroTarjeta']) && (array_blank($validDatos)))
        or blank($validDatos['passwd'])) 
            return err::EMPTY;

    // Valida los datos.
    if (!validacion($validDatos))
        return err::VALIDATION;

    // Devuelve un codigo de error si la contraseña no coincide.
    if (md5($validDatos['passwd']) != traerPasswd($datos['email']))
        return err::NO_MATCH;

    if (isset($validDatos['numeroTarjeta'], $_POST['permanecer']) && $_POST['permanecer'] == true
        and !nuevaCC($datos['email'], array_pick($validDatos, ['numeroTarjeta', 'banco'])))
            return err::DB_ERR;

    // Asigna los datos restantes.
    $datos['fechaCompra'] = date("Y-m-d");
    $datos['precio'] = obtenerTotal($carritoDB);
    if (isset($carritoDB['idFuncion'])) {
        $datos['idFuncion'] = $carritoDB['idFuncion'];
        $datos['asientos'] = $carritoDB['asientos'];
    }
    // Intenta confirmar la compra.
    if (!(nuevaCompra($datos) && eliminarCarrito($carritoDB['email'])))
        return err::DB_ERR;
    if (isset($carritoDB['articulos'])
        and !nuevaCompraArt($datos['idCompra'], $carritoDB['articulos']))
            return err::DB_ERR;
    if (isset($carritoDB['idFuncion'])
        and !comprarAsientos($datos))
            return err::DB_ERR;

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
    $factura['email'] = $carritoDB['email'];
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

    return $factura;
}

function validacion($datos)
{
    // Valida la contraseña, verificando que solo contenga caracteres permitidos y su longitud este en el rango permitido.
    if (!validarStr($datos['passwd'], 12))
        return false;
    if (isset($datos['numeroTarjeta'])) {
        // Valida la tarjeta ingresada, verificando que este en el formato permitido y su longitud este en el rango permitido.
        if (!validarInt($datos['numeroTarjeta'], 16) || strlen($datos['numeroTarjeta']) != 16)
        return false;

        // Valida el banco ingresado, verificando que este en el formato permitido y su longitud este en el rango permitido.
        if (!validarAl($datos['banco'],20))
            return false;
    }

    // Si todos los campos estan bien, retorna true.
    return true;
}