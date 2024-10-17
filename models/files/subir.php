<?php

// Este script inserta en sus carpetas correspondientes los archivos del usuario que necesitan de permanencia.

// Valida los datos y sube la imagen ingresada.
function subirImg($img, $nombre, $carpeta)
{
    $dir = "../../views/assets/img/$carpeta/$nombre";

    // Verifica que la imagen haya sido subida correctamente a la página.
    if ($img['error'] != UPLOAD_ERR_OK)
        return false;

    // Intenta subir la imagen y de no lograrlo devuelve falso.
    return move_uploaded_file($img['tmp_name'], $dir);
}

function borrarImg($nombre, $carpeta)
{
    $dir = "../../views/assets/img/$carpeta/$nombre";
    
    // Si el archivo existe y se puede borrar, lo hace, de haber un imprevisto devuelve falso.
    return (file_exists($dir) && is_writable($dir)) ?
        unlink($dir) : false;
}

// Actualiza la imagen, borrando su instancia anterior y subiendo una nueva.
function actImg($img, $nombreNuevo, $nombreViejo, $carpeta)
{
    return borrarImg($nombreViejo, $carpeta) && subirImg($img, $nombreNuevo, $carpeta);
}

function actNombreImg($nombreNuevo, $nombreViejo, $carpeta)
{
    $dirVieja = "../../views/assets/img/$carpeta/$nombreViejo";
    $dirNueva = "../../views/assets/img/$carpeta/$nombreNuevo";
    return rename($dirVieja, $dirNueva);
}