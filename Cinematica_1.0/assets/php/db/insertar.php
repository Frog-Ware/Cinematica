<?php

// Este script inserta en la base de datos los datos del usuario que necesitan de permanencia.

require_once "conn.php";

// Funciones de manipulación de datos de usuario.

// Registra un usuario en la base de datos.
function nuevoUsuario($datos) {
    $lineaSql = "INSERT INTO usuario(email, nombre, apellido, imagenPerfil, passwd, numeroCelular, token) VALUES (?, ?, ?, ?, ?, ?, ?)";
    return insertar($datos, $lineaSql);
}

// Registra un usuario del tipo cliente en la base de datos.
function nuevoCliente($datos) {
    $lineaSql = "INSERT INTO cliente(email) VALUES (?)";
    return (nuevoUsuario($datos)) ?
        insertar([$datos['email']], $lineaSql) : false;
}

// Actualiza la contraseña del usuario.
function actPasswd($datos) {
    $lineaSql = "UPDATE Usuario SET passwd = ? WHERE email = ?";
    return insertar([$datos['passwd'], $datos['email']], $lineaSql);   
}



// Funciones de manipulación de datos de productos.

// Ingresa un producto en la base de datos.
function nuevoProducto($idProducto) {
    $lineaSql = "INSERT INTO producto (idProducto) VALUES (?)";
    return insertar([$idProducto], $lineaSql);
}

// Ingresa un producto de tipo película en la base de datos.
function nuevaPelicula($datos, $valores) {
    if (!nuevoProducto($datos['idProducto'])) return false;
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

// Ingresa un producto de tipo artículo en la base de datos.
function nuevoArticulo($datos) {
    $lineaSql = "INSERT INTO articulo VALUES (?, ?, ?, ?, ?)";
    return nuevoProducto($datos['idProducto']) ? 
        insertar($datos, $lineaSql) : false;
}

// Ingresa una película ya existente en la cartelera.
function nuevaEnCartelera($datos) {
    $lineaSql = "INSERT INTO cartelera VALUES (?, ?, ?)";
    return insertar($datos, $lineaSql);
}



// Funciones de acceso a la base de datos.

// Inserta los datos enviados según la linea de código provista.
function insertar($datos, $lineaSql) {
    global $con;
    try{
        $statement = $con->prepare($lineaSql);
        return $statement -> execute(array_values($datos));
    } catch (PDOException $pe) {
        return false;
    }
}