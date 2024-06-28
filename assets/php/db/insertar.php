<?php

// Este script inserta en la base de datos los datos del usuario que necesitan de permanencia.

require ("conn.php");

// Registra un usuario en la base de datos.
function nuevoUsuario($datos) {
    $lineaSql = "INSERT INTO usuario(email, nombre, apellido, imagenPerfil, passwd, numeroCelular, token) values (?, ?, ?, ?, ?, ?, ?)";
    return insertar($datos, $lineaSql);
}

// Registra un usuario del tipo cliente en la base de datos.
function nuevoCliente($datos) {
    nuevoUsuario($datos);
    $lineaSql = "INSERT INTO cliente(email) values (?)";
    return insertar([$datos['email']], $lineaSql);
}

// Ingresa un producto en la base de datos.
function nuevoProducto($idProducto) {
    $lineaSql = "INSERT INTO producto (idProducto) values (?)";
    return insertar([$idProducto], $lineaSql);
}

// Ingresa un producto de tipo película en la base de datos.
function nuevaPelicula($datos, $valores) {
    nuevoProducto($datos['idProducto']);
    $lineaSql = "INSERT INTO pelicula values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if (insertar($datos, $lineaSql)) {
        // De ingresar la pelicula, registra sus categorias.
        $lineaSql = "INSERT INTO tieneCategorias values (?, ?)";
        foreach ($valores['categorias'] as $x) {
            if (!insertar([$x, $datos['idProducto']], $lineaSql)) return false;
        }
        // Registra la disponibilidad de 2D, 3D, etc.
        $lineaSql = "INSERT INTO tieneDimensiones values (?, ?)";
        foreach ($valores['dimensiones'] as $x) {
            if (!insertar([$x, $datos['idProducto']], $lineaSql)) return false;
        }
        // Registra la disponibilidad de idiomas.
        $lineaSql = "INSERT INTO tieneIdiomas values (?, ?)";
        foreach ($valores['idiomas'] as $x) {
            if (!insertar([$x, $datos['idProducto']], $lineaSql)) return false;
        }
    } else return false;
}

function insertar($datos, $lineaSql) {
    global $con;
    try{
        $statement = $con->prepare($lineaSql);
        return $statement -> execute(array_values($datos));
    } catch (PDOException $pe) {
        echo json_encode(['mensaje' => "Error en insertar.php, nuevoUsuario():" . $pe->getMessage()]);
        return false;
    }
}

?>