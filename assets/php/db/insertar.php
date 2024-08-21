<?php

// Este script inserta en la base de datos los datos del usuario que necesitan de permanencia.

require_once "conn.php";
require_once "../config/acceso.php";

// Funciones de manipulación de datos de usuario.

// Registra un usuario en la base de datos.
function nuevoUsuario($datos)
{
    $lineaSql = "INSERT INTO usuario(email, nombre, apellido, imagenPerfil, passwd, numeroCelular, token) VALUES (?, ?, ?, ?, ?, ?, ?)";
    return insertar($datos, $lineaSql);
}

// Registra un usuario del tipo cliente en la base de datos.
function nuevoCliente($datos)
{
    $lineaSql = "INSERT INTO cliente(email) VALUES (?)";
    return (nuevoUsuario($datos)) ?
        insertar([$datos['email']], $lineaSql) : false;
}

// Actualiza la contraseña del usuario.
function actPasswd($datos)
{
    $lineaSql = "UPDATE Usuario SET passwd = ? WHERE email = ?";
    return insertar([$datos['passwd'], $datos['email']], $lineaSql);
}

function actImagen($datos)
{
    $lineaSql = "UPDATE Usuario SET imagenPerfil = ? WHERE email = ?";
    return insertar($datos, $lineaSql);
}



// Funciones de manipulación de datos de productos.

// Ingresa un producto en la base de datos.
function nuevoProducto($idProducto)
{
    $lineaSql = "INSERT INTO producto (idProducto) VALUES (?)";
    return insertar([$idProducto], $lineaSql);
}

// Ingresa un producto de tipo película en la base de datos.
function nuevaPelicula($datos, $valores)
{
    if (!nuevoProducto($datos['idProducto']))
        return false;
    $lineaSql = "INSERT INTO pelicula VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if (insertar($datos, $lineaSql)) {
        // De ingresar la pelicula, registra sus categorias.
        $lineaSql = "INSERT INTO tieneCategorias VALUES (?, ?)";
        foreach ($valores['categorias'] as $x)
            if (!insertar([$x, $datos['idProducto']], $lineaSql))
                return false;

        // Registra la disponibilidad de 2D, 3D, etc.
        $lineaSql = "INSERT INTO tieneDimensiones VALUES (?, ?)";
        foreach ($valores['dimensiones'] as $x)
            if (!insertar([$x, $datos['idProducto']], $lineaSql))
                return false;

        // Registra la disponibilidad de idiomas.
        $lineaSql = "INSERT INTO tieneIdiomas VALUES (?, ?)";
        foreach ($valores['idiomas'] as $x)
            if (!insertar([$x, $datos['idProducto']], $lineaSql))
                return false;

        // De insertar todo correctamente en la base de datos, devuelve true.
        return true;
    } else
        return false;
}

// Actualiza un producto de tipo película en la base de datos.
function actPelicula($datos, $valores, $idProducto)
{
    // Si debe, actualiza los datos de las películas.
    if (!empty($datos)) {
        $set = implode(" = ?, ", array_keys($datos)) . " = ?";
        $datos['idProducto'] = $idProducto;
        $lineaSql = "UPDATE pelicula SET $set WHERE idProducto = ?";
        if (!insertar($datos, $lineaSql))
            return false;
    }
    // Si debe, actualiza las categorías.
    if (!empty($valores['categorias'])) {
        $del = "DELETE FROM tieneCategorias WHERE idProducto = ?";
        if (!insertar([$idProducto], $del))
            return false;
        $lineaSql = "INSERT INTO tieneCategorias VALUES (?, ?)";
        foreach ($valores['categorias'] as $x)
            if (!insertar([$x, $idProducto], $lineaSql))
                return false;
    }
    // Si debe, actualiza la disponibilidad de 2D, 3D, etc.
    if (!empty($valores['dimensiones'])) {
        $del = "DELETE FROM tieneDimensiones WHERE idProducto = ?";
        if (!insertar([$idProducto], $del))
            return false;
        $lineaSql = "INSERT INTO tieneDimensiones VALUES (?, ?)";
        foreach ($valores['dimensiones'] as $x)
            if (!insertar([$x, $idProducto], $lineaSql))
                return false;
    }
    // Si debe, actualiza la disponibilidad de idiomas.
    if (!empty($valores['idiomas'])) {
        $del = "DELETE FROM tieneIdiomas WHERE idProducto = ?";
        if (!insertar([$idProducto], $del))
            return false;
        $lineaSql = "INSERT INTO tieneIdiomas VALUES (?, ?)";
        foreach ($valores['idiomas'] as $x)
            if (!insertar([$x, $idProducto], $lineaSql))
                return false;
    }
    // De insertar todo correctamente en la base de datos, devuelve true.
    return true;
}

// Elimina un producto de tipo película en la base de datos.
function eliminarPelicula($idProducto)
{
    $tablas = ['funciones', 'tieneCategorias', 'tieneDimensiones', 'tieneIdiomas', 'cartelera', 'pelicula', 'producto'];
    foreach ($tablas as $x) {
        $lineaSql = "DELETE FROM $x WHERE idProducto = ?";
        if (!insertar([$idProducto], $lineaSql))
            return false;
    }
    return true;
}

// Ingresa un producto de tipo artículo en la base de datos.
function nuevoArticulo($datos)
{
    $lineaSql = "INSERT INTO articulo VALUES (?, ?, ?, ?, ?)";
    return nuevoProducto($datos['idProducto']) ?
        insertar($datos, $lineaSql) : false;
}

// Actualiza un producto de tipo película en la base de datos.
function actArticulo($datos, $idProducto)
{
    $set = implode(" = ?, ", array_keys($datos)) . " = ?";
    $datos['idProducto'] = $idProducto;
    $lineaSql = "UPDATE articulo SET $set WHERE idProducto = ?";
    return insertar($datos, $lineaSql);
}

// Elimina un producto de tipo artículo en la base de datos.
function eliminarArticulo($idProducto)
{
    $tablas = ['articulo', 'producto'];
    foreach ($tablas as $x) {
        $lineaSql = "DELETE FROM $x WHERE idProducto = ?";
        if (!insertar([$idProducto], $lineaSql))
            return false;
    }
    return true;
}

// Ingresa una película ya existente en la cartelera.
function nuevaEnCartelera($datos)
{
    $lineaSql = "INSERT INTO cartelera VALUES (?, ?, ?)";
    return insertar($datos, $lineaSql);
}

// Crea o actualiza el carrito en la base de datos.
function actCarrito($datos, $nuevo)
{
    $lineaSql = $nuevo ?
        "INSERT INTO carrito (idFuncion, asientos, email) VALUES (?, ?, ?)" :
        "UPDATE carrito SET idFuncion = ?, asientos = ? WHERE email = ?";
    return insertar($datos, $lineaSql);
}

// Guarda artículos en el carrito.
function actCarritoArt($email, $datos)
{
    $lineaSql = "INSERT INTO carritoArticulo VALUES (\"$email\", ?, ?)";
    foreach ($datos as $x) 
        if (!insertar($x, $lineaSql)) return false;
    return true;
}



// Funciones de acceso a la base de datos.

// Inserta los datos enviados según la linea de código provista.
function insertar($datos, $lineaSql)
{
    global $con;
    try {
        $statement = $con->prepare($lineaSql);
        return $statement->execute(array_values($datos));
    } catch (PDOException $pe) {
        return false;
    }
}