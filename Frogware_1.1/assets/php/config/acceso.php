<?php

// Este script restringe el acceso si no se utiliza el encabezado y método adecuados.

/*
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.0 405 Method Not Allowed');
    exit;
}
*/