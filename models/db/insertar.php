<?php

// Este script inserta en la base de datos los datos del usuario que necesitan de permanencia.

require_once "conn.php";
require_once "../../models/utilities/validacion.php";

// Funciones de manipulación de datos de usuario.

// Registra un usuario en la base de datos.
function nuevoUsuario($datos)
{
    $lineaSql = "INSERT INTO Usuario (email, nombre, apellido, imagenPerfil, passwd, numeroCelular, token) VALUES (?, ?, ?, ?, ?, ?, ?)";
    return insertar($datos, $lineaSql);
}

// Registra un usuario del tipo cliente en la base de datos.
function nuevoCliente($datos)
{
    $lineaSql = "INSERT INTO Cliente (email) VALUES (?)";
    return (nuevoUsuario($datos)) ?
        insertar([$datos['email']], $lineaSql) : false;
}

// Actualiza la contraseña del usuario.
function actPasswd($datos)
{
    $lineaSql = "UPDATE Usuario SET passwd = ? WHERE email = ?";
    return insertar($datos, $lineaSql);
}

// Actualiza la imagen del usuario.
function actImagenPerfil($datos)
{
    $lineaSql = "UPDATE Usuario SET imagenPerfil = ? WHERE email = ?";
    return insertar($datos, $lineaSql);
}

// Agrega otra opcion como imagen de perfil.
function nuevaPFP($nmb)
{
    $lineaSql = "INSERT INTO ImagenPerfil (imagenPerfil) VALUES (?)";
    return insertar([$nmb], $lineaSql);
}

function eliminarPFP($nmb)
{
    $lineaSql = "DELETE FROM ImagenPerfil WHERE imagenPerfil = ?";
    return insertar([$nmb], $lineaSql);
}

function nuevoSlider($nmb)
{
    $lineaSql = "INSERT INTO ImagenSlider (imagenSlider) VALUES (?)";
    return insertar([$nmb], $lineaSql);
}

function eliminarSlider($nmb)
{
    $lineaSql = "DELETE FROM ImagenSlider WHERE imagenSlider = ?";
    return insertar([$nmb], $lineaSql);
}

// Actualiza los datos del usuario.
function actUsuario($datos, $email)
{
    $set = implode(" = ?, ", array_keys($datos)) . " = ?";
    $lineaSql = "UPDATE Usuario SET $set WHERE email = \"$email\"";
    return insertar($datos, $lineaSql);
}

function cambiarRol($datos, $antRol)
{
    list($email, $rol) = array_values($datos);
    switch ($rol) {
        case 0:
            $lineaSql = "DELETE FROM Empleado WHERE email = ?";
            if (!insertar([$email], $lineaSql))
                return false;
            $lineaSql = "INSERT INTO Cliente (email) VALUES (?)";
            return insertar([$email], $lineaSql);
        case 1:
            if (!$antRol) {
                foreach (['CarritoArticulo', 'Carrito', 'Cliente'] as $x) {
                    $lineaSql = "DELETE FROM $x WHERE email = ?";
                    if (!insertar([$email], $lineaSql))
                        return false;
                }
                $lineaSql = "INSERT INTO Empleado (email, esAdmin) VALUES (?, ?)";
                return insertar([$email, 0], $lineaSql);
            } else {
                $lineaSql = "UPDATE Empleado SET esAdmin = 0 WHERE email = ?";
                return insertar([$email], $lineaSql);
            }
        case 2:
            if (!$antRol) {
                foreach (['CarritoArticulo', 'Carrito', 'Cliente'] as $x) {
                    $lineaSql = "DELETE FROM $x WHERE email = ?";
                    if (!insertar([$email], $lineaSql))
                        return false;
                }
                $lineaSql = "INSERT INTO Empleado (email, esAdmin) VALUES (?, ?)";
                return insertar([$email, 1], $lineaSql);
            } else {
                $lineaSql = "UPDATE Empleado SET esAdmin = 1 WHERE email = ?";
                return insertar([$email], $lineaSql);
            }
    }
    
}



// Funciones de manipulación de datos de productos.

// Ingresa un producto en la base de datos.
function nuevoProducto($idProducto)
{
    $lineaSql = "INSERT INTO Producto (idProducto) VALUES (?)";
    return insertar([$idProducto], $lineaSql);
}

// Ingresa un producto de tipo película en la base de datos.
function nuevaPelicula($datos, $valores)
{
    if (!nuevoProducto($datos['idProducto']))
        return false;
    $lineaSql = "INSERT INTO Pelicula VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if (insertar($datos, $lineaSql)) {
        // De ingresar la pelicula, registra sus categorias.
        $lineaSql = "INSERT INTO TieneCategorias VALUES (?, ?)";
        foreach ($valores['categorias'] as $x)
            if (!insertar([$x, $datos['idProducto']], $lineaSql))
                return false;

        // Registra la disponibilidad de 2D, 3D, etc.
        $lineaSql = "INSERT INTO TieneDimensiones VALUES (?, ?)";
        foreach ($valores['dimensiones'] as $x)
            if (!insertar([$x, $datos['idProducto']], $lineaSql))
                return false;

        // Registra la disponibilidad de idiomas.
        $lineaSql = "INSERT INTO TieneIdiomas VALUES (?, ?)";
        foreach ($valores['idiomas'] as $x)
            if (!insertar([$x, $datos['idProducto']], $lineaSql))
                return false;

        // De insertar todo correctamente en la base de datos, devuelve true.
        return true;
    } else
        return false;
}

// Actualiza un producto de tipo película en la base de datos.
function actPelicula($datos, $datosArr, $idProducto)
{
    // Si debe, actualiza los datos de las películas.
    if (!blank($datos)) {
        $set = implode(" = ?, ", array_keys($datos)) . " = ?";
        $datos['idProducto'] = $idProducto;
        $lineaSql = "UPDATE Pelicula SET $set WHERE idProducto = ?";
        if (!insertar($datos, $lineaSql))
            return false;
    }
    // Si debe, actualiza las demas tablas relacionadas.
    $tablas = ['categorias', 'dimensiones', 'idiomas'];
    foreach ($tablas as $t) {
        $insertSql = "INSERT INTO Tiene$t VALUES (?, ?)";
        $deleteSql = "DELETE FROM Tiene$t WHERE idProducto = ?";
        if (isset($datosArr[$t]) && !blank($datosArr[$t])) {
            if (!insertar([$idProducto], $deleteSql))
                return false;
            foreach ($datosArr[$t] as $x)
                if (!insertar([$x, $idProducto], $insertSql))
                    return false;
        }
    }

    // De insertar todo correctamente en la base de datos, devuelve true.
    return true;
}

// Elimina un producto de tipo película en la base de datos.
function eliminarPelicula($idProducto)
{
    $tablas = ['TieneCategorias', 'TieneDimensiones', 'TieneIdiomas', 'Cartelera', 'Pelicula', 'Producto'];
    foreach ($tablas as $x) {
        $lineaSql = "DELETE FROM $x WHERE idProducto = ?";
        if (!insertar([$idProducto], $lineaSql))
            return false;
    }
    return true;
}

// Cambia el precio de las peliculas de una dimension en esp.
function cambiarPrecio($dim, $precio)
{
    $lineaSql = "UPDATE Dimensiones SET precio = ? WHERE dimension = ?";
    return insertar([$precio, $dim], $lineaSql);
}

// Ingresa un producto de tipo artículo en la base de datos.
function nuevoArticulo($datos)
{
    $lineaSql = "INSERT INTO Articulo VALUES (?, ?, ?, ?, ?)";
    return nuevoProducto($datos['idProducto']) ?
        insertar($datos, $lineaSql) : false;
}

// Actualiza un producto de tipo película en la base de datos.
function actArticulo($datos, $idProducto)
{
    $set = implode(" = ?, ", array_keys($datos)) . " = ?";
    $datos['idProducto'] = $idProducto;
    $lineaSql = "UPDATE Articulo SET $set WHERE idProducto = ?";
    return insertar($datos, $lineaSql);
}

// Elimina un producto de tipo artículo en la base de datos.
function eliminarArticulo($idProducto)
{
    $tablas = ['carritoArticulo', 'articulo', 'producto'];
    foreach ($tablas as $x) {
        $lineaSql = "DELETE FROM $x WHERE idProducto = ?";
        if (!insertar([$idProducto], $lineaSql))
            return false;
    }
    return true;
}

// Ingresa una película ya existente en la cartelera.
function nuevaEnCartelera($idProducto)
{
    $lineaSql = "INSERT INTO Cartelera VALUES (?)";
    return insertar([$idProducto], $lineaSql);
}

function eliminarEnCartelera($idProducto)
{
    $lineaSql = "DELETE FROM Cartelera WHERE idProducto = ?";
    return insertar([$idProducto], $lineaSql);
}

// Crea o actualiza el carrito en la base de datos.
function actCarrito($datos, $nuevo)
{
    $lineaSql = $nuevo ?
        "INSERT INTO Carrito (idFuncion, asientos, email) VALUES (?, ?, ?)" :
        "UPDATE Carrito SET idFuncion = ?, asientos = ? WHERE email = ?";
    return insertar($datos, $lineaSql);
}

// Guarda artículos en el carrito.
function actCarritoArt($email, $datos)
{
    $lineaSql = "INSERT INTO carritoArticulo VALUES ('$email', ?, ?)";
    foreach ($datos as $x)
        if (!insertar($x, $lineaSql))
            return false;
    return true;
}

// Elimina el carrito.
function eliminarCarrito($email)
{
    $tablas = ['carritoarticulo', 'carrito'];
    foreach ($tablas as $x) {
        $lineaSql = "DELETE FROM $x WHERE email = ?";
        if (!insertar([$email], $lineaSql))
            return false;
    }
    return true;
}

// Registra una nueva compra.
function nuevaCompra($datos)
{
    $lineaSql = "INSERT INTO Compra VALUES (?, ?, ?, ?, ?, ?)";
    return insertar($datos, $lineaSql);
}

// Agrega una nueva función.
function nuevaFunc($datos)
{
    $lineaSql = "INSERT INTO Funciones (idFuncion, idProducto, nombreCine, numeroSala, fechaPelicula, horaPelicula, dimension) VALUES (?, ?, ?, ?, ?, ?, ?)";
    return insertar($datos, $lineaSql);
}

function eliminarFunc($idFuncion)
{
    foreach (['carrito', 'funciones'] as $x) {
        $lineaSql = "DELETE FROM $x WHERE idFuncion = ?";
        if (!insertar([$idFuncion], $lineaSql))
            return false;
    }
    return true;
}

// Elimina la función según id de las películas implicadas.
function eliminarFuncEsp($idProducto)
{
    $lineaSql = "DELETE FROM Funciones WHERE idProducto = ?";
    return insertar([$idProducto], $lineaSql);
}

// Agrega asientos ocupados en una función.
function reservarAsiento($datos, $anteriores)
{
    if (!is_null($anteriores))
        eliminarAsientos($anteriores);
    $lineaSql = "INSERT INTO Asientos (fila, columna, idFuncion, vendido) VALUES (?, ?, ?, 0)";
    if (str_contains($datos['asientos'], ", ")) {
        foreach (explode(", ", $datos['asientos']) as $x)
            if (!insertar(array_merge(explode("-", $x), [$datos['idFuncion']]), $lineaSql))
                return false;
        return true;
    } else {
        return insertar(array_merge(explode("-", $datos['asientos']), [$datos['idFuncion']]), $lineaSql);
    }  
}

function comprarAsientos($datos)
{
    $lineaSql = "UPDATE Asientos SET vendido = 1 WHERE fila = ? AND columna = ? AND idFuncion = ?";
    if (str_contains($datos['asientos'], ", ")) {
        foreach (explode(", ", $datos['asientos']) as $x)
            if (!insertar(array_merge(explode("-", $x), [$datos['idFuncion']]), $lineaSql))
                return false;
        return true;
    } else {
        return insertar(array_merge(explode("-", $datos['asientos']), [$datos['idFuncion']]), $lineaSql);
    }  
}

// Elimina asientos ocupados en una función.
function eliminarAsientos($datos)
{
    $lineaSql = "DELETE FROM Asientos WHERE fila = ? AND columna = ? AND idFuncion = ?";
    if (str_contains($datos['asientos'], ", ")) {
        foreach (explode(", ", $datos['asientos']) as $x)
            if (!insertar(array_merge(explode("-", $x), [$datos['idFuncion']]), $lineaSql))
                return false;
        return true;
    } else {
        return insertar(array_merge(explode("-", $datos['asientos']), [$datos['idFuncion']]), $lineaSql);
    }  
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