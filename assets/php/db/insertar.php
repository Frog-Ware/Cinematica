<?php

// Este script inserta en la base de datos los datos del usuario que necesitan de permanencia.

require_once "conn.php";

// Registra un usuario en la base de datos.
function nuevoUsuario($datos) {
    $lineaSql = "INSERT INTO usuario(email, nombre, apellido, imagenPerfil, passwd, numeroCelular, token) VALUES (?, ?, ?, ?, ?, ?, ?)";
    return insertar($datos, $lineaSql);
}

// Registra un usuario del tipo cliente en la base de datos.
function nuevoCliente($datos) {
    nuevoUsuario($datos);
    $lineaSql = "INSERT INTO cliente(email) VALUES (?)";
    return insertar([$datos['email']], $lineaSql);
}

// Ingresa un producto en la base de datos.
function nuevoProducto($idProducto) {
    $lineaSql = "INSERT INTO producto (idProducto) VALUES (?)";
    return insertar([$idProducto], $lineaSql);
}

// Ingresa un producto de tipo película en la base de datos.
function nuevaPelicula($datos, $valores) {
    nuevoProducto($datos['idProducto']);
    $lineaSql = "INSERT INTO pelicula VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if (insertar($datos, $lineaSql)) {
        // De ingresar la pelicula, registra sus categorias.
        $lineaSql = "INSERT INTO tieneCategorias VALUES (?, ?)";
        foreach ($valores['categorias'] as $x)
            if (!insertar([$x, $datos['idProducto']], $lineaSql)) return false;

        // Registra la disponibilidad de 2D, 3D, etc.
        $lineaSql = "INSERT INTO tieneDimensiones VALUES (?, ?)";
        foreach ($valores['dimensiones'] as $x)
            if (!insertar([$x, $datos['idProducto']], $lineaSql)) return false;

        // Registra la disponibilidad de idiomas.
        $lineaSql = "INSERT INTO tieneIdiomas VALUES (?, ?)";
        foreach ($valores['idiomas'] as $x)
            if (!insertar([$x, $datos['idProducto']], $lineaSql)) return false;

        // De insertar todo correctamente en la base de datos, devuelve true.
        return true;

    } else return false;
}

function nuevoArticulo($datos) {
    nuevoProducto($datos['idProducto']);
    $lineaSql = "INSERT INTO articulo VALUES (?, ?, ?, ?, ?)";
    return insertar($datos, $lineaSql);
}

function nuevaEnCartelera($datos) {
    $lineaSql = "INSERT INTO cartelera VALUES (?, ?, ?)";
    return insertar($datos, $lineaSql);
}

function actPasswd($datos) {
    $lineaSql = "UPDATE Usuario SET passwd = ? WHERE email = ?";
    return insertar([md5($datos['passwd']), $datos['email']], $lineaSql);   
}


function insertar($datos, $lineaSql) {
    global $con;
    try{
        $statement = $con->prepare($lineaSql);
        return $statement -> execute(array_values($datos));
    } catch (PDOException $pe) {
        echo json_encode(['mensaje' => "Error en insertar.php, insertar():" . $pe->getMessage()]);
        return false;
    }
}