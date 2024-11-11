<?php

// Este script inserta en sus carpetas correspondientes los archivos del usuario que necesitan de permanencia.

// Valida los datos y sube el archivo ingresado.
function subirFile($file, $nombre, $carpeta)
{
    $dir = "../../views/assets/$carpeta/$nombre";

    // Verifica que el archivo haya sido subido correctamente a la página.
    if ($file['error'] != UPLOAD_ERR_OK)
        return false;

    // Intenta subir el archivo y de no lograrlo devuelve falso.
    return move_uploaded_file($file['tmp_name'], $dir);
}

function borrarFile($nombre, $carpeta)
{
    $dir = "../../views/assets/$carpeta/$nombre";
    
    // Si el archivo existe y se puede borrar, lo hace, de haber un imprevisto devuelve falso.
    return (file_exists($dir) && is_writable($dir)) ?
        unlink($dir) : false;
}

// Actualiza el archivo, borrando su instancia anterior y subiendo una nueva.
function actFile($file, $nombreNuevo, $nombreViejo, $carpeta)
{
    return borrarFile($nombreViejo, $carpeta) && subirFile($file, $nombreNuevo, $carpeta);
}

function actNombreFile($nombreNuevo, $nombreViejo, $carpeta)
{
    $dirVieja = "../../views/assets/$carpeta/$nombreViejo";
    $dirNueva = "../../views/assets/$carpeta/$nombreNuevo";
    return rename($dirVieja, $dirNueva);
}

function checkFile($nombre, $carpeta)
{
    $dir = "../../views/assets/$carpeta/$nombre";
    return file_exists($dir) && is_writable($dir);
}