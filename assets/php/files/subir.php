<?php

// Este script inserta en sus carpetas correspondientes los archivos del usuario que necesitan de permanencia.

// Valida los datos y sube la imagen ingresada.
function subirImg($img, $nombre, $carpeta) {
    // Verifica que la imagen haya sido subida correctamente a la página.
    if ($img['error'] != UPLOAD_ERR_OK) return false;

    // Verifica que la imagen sea JPG o PNG.
    if (!in_array(mime_content_type($img['tmp_name']), ['image/jpeg', 'image/png'])) return false;

    // Verifica que la imagen sea menor en tamaño a los 10MB.
    if ($img['size'] > 10 * 1024 * 1024) return false;
    
    // Intenta subir la imagen y de no lograrlo devuelve falso.
    $dir = '../../img/' . $carpeta . '/' . $nombre;
    return move_uploaded_file($img['tmp_name'], $dir);
}