<?php

// Este script inserta en sus carpetas correspondientes los archivos del usuario que necesitan de permanencia.

require_once "../config/acceso.php";

// Valida los datos y sube la imagen ingresada.
function subirImg($img, $nombre, $carpeta)
{
    // Verifica que la imagen haya sido subida correctamente a la página.
    if ($img['error'] != UPLOAD_ERR_OK)
        return false;

    // Verifica que la imagen sea JPG o PNG.
    if (mime_content_type($img['tmp_name']) !== 'image/webp')
        return false;

    // Verifica que la imagen sea menor en tamaño a los 200KB.
    if ($img['size'] > 200 * 1024)
        return false;

    // Intenta subir la imagen y de no lograrlo devuelve falso.
    $dir = "../../img/$carpeta/$nombre";
    return move_uploaded_file($img['tmp_name'], $dir);
}

function borrarImg($nombre, $carpeta)
{
    $dir = "../../img/$carpeta/$nombre";
    if (!file_exists($dir))
        return true;
    return is_writable($dir) ?
        unlink($dir) : false;
}

// Actualiza la imagen, borrando su instancia anterior y subiendo una nueva.
function updImg($img, $nombreNuevo, $nombreViejo, $carpeta)
{
    return borrarImg($nombreViejo, $carpeta) && subirImg($img, $nombreNuevo, $carpeta);
}