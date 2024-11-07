<?php

// Este script devuelve un array con distintas estadísticas sobre la venta de entradas.

ob_start();
header("Content-Type: application/json; charset=utf-8");
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once "../../models/db/traer.php";
require_once "../../models/utilities/validacion.php";

// Asigna un código de error según el caso.
enum err: int
{
    case SUCCESS = 0;
    case NO_SUCCESS = 1;

    // Devuelve el mensaje asociado con el código de error.
    function getMsg()
    {
        return match ($this) {
            self::SUCCESS => "Procedimiento realizado con éxito.",
            self::NO_SUCCESS => "No hay peliculas disponibles."
        };
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    isset($_SESSION['user']) && traerRol($_SESSION['user']) != 0 ?
        main() : header('HTTP/1.1 401 Unauthorized', true, 401);
} else {
    // Restringe el acceso si no se utiliza el método de solicitud adecuado.
    header('HTTP/1.0 405 Method Not Allowed', true, 405);
}

exit;

// Funciones


function main()
{
    $func = traerFuncLista();
    foreach ($func as $x){
        foreach (['peliculas' => 'idProducto', 'fecha' => 'fechaPelicula', 'cine' => 'nombreCine'] as $k => $v) {
            $stats[$k][$x[$v]]['cantFunc'] = ($stats[$k][$x[$v]]['cantFunc'] ?? 0) + 1;
            $stats[$k][$x[$v]]['cantEntradas'] = ($stats[$k][$x[$v]]['cantEntradas'] ?? 0) + traerSala($x['nombreCine'], $x['numeroSala'])['disp'] - $x['disp'];
        }
        $dia = date('l', strtotime($x['fechaPelicula']));
        $stats['dia'][$dia]['cantFunc'] = ($stats['dia'][$dia]['cantFunc'] ?? 0) + 1;
        $stats['dia'][$dia]['cantEntradas'] = ($stats['dia'][$dia]['cantEntradas'] ?? 0) + traerSala($x['nombreCine'], $x['numeroSala'])['disp'] - $x['disp'];
    }
    
    // Actualiza el log y limpia el buffer.
    file_put_contents('../../log.txt', crearLog(ob_get_clean(), basename(__FILE__)), FILE_APPEND);

    // Devuelve un JSON con la respuesta.
    echo json_encode($stats);
}