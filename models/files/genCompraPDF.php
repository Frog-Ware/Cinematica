<?php

use Dompdf\Dompdf;
require '../../vendor/autoload.php';

function genPDF($datos)
{
    $dompdf = new Dompdf();
    $dompdf->loadHtml(getHTML($datos));
    $dompdf->setPaper('A4', 'landscape');

    // Renderiza el HTML como un PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream("compra" . $datos['idCompra'] . ".pdf", ['Attachment' => true]);
}

function getHTML($datos)
{
    ob_start();
    include 'pdf.php';
    return ob_get_clean();
}

