<?php

// Genera un PDF para la compra.

use Dompdf\Dompdf;
require_once '../../vendor/autoload.php';

function generarPDF($datos)
{
    $dompdf = new Dompdf();
    $pdf = function($datos) {
        ob_start();
        include 'plantillapdf.php';
        return ob_get_clean();
    };
    $dompdf->loadHtml($pdf($datos));
    $dompdf->setPaper('A4', 'landscape');

    // Renderiza el HTML como un PDF
    $dompdf->render();

    return $dompdf->output();
}