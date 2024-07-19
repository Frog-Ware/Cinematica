<?php

// Este script devuelve los datos requeridos por diferentes scripts.

require_once "conn.php";

// Devuelve la contraseña asociada al usuario poseedor del email ingresado.
function traerPasswd($email) {
    $consultaSql = "SELECT passwd FROM Usuario WHERE email = ?";
    $datos = consultaClave($consultaSql, [$email]);
    return (!empty($datos) && isset($datos['passwd'])) ?
        $datos['passwd'] : null;
}

// Devuelve los datos asociados al usuario poseedor del email ingresado.
function traerUsuario($email) {
    $consultaSql = "SELECT email, nombre, apellido, imagenPerfil FROM Usuario WHERE email = ?";
    $datos = consultaClave($consultaSql, [$email]);
    return (!empty($datos)) ?
        $datos : null;
}

// Devuelve el rol del usuario en cuestion.
function traerRol($email) {
    $consultaSql = "SELECT email, 1 AS rol FROM Administrador WHERE email = ? UNION ALL SELECT email, 0 AS rol FROM Cliente WHERE email = ?";
    $datos = consultaClave($consultaSql, [$email, $email]);
    return (!empty($datos) && isset($datos['rol'])) ?
        $datos['rol'] : null;
}

function traerToken($email) {
    $consultaSql = "SELECT token FROM Usuario WHERE email = ?";
    $datos = consultaClave($consultaSql, [$email]);
    return (!empty($datos) && isset($datos['token'])) ?
        $datos['token'] : null;
}

// Devuelve la pelicula asociada al nombre.
function traerPelicula($id) {
    $consultaSql = "SELECT * FROM Pelicula WHERE idProducto = ?";
    $datos = consultaClave($consultaSql, [$id]);
    return (!empty($datos)) ?
        $datos : null;
}

// Devuelve la cartelera entera.
function traerCartelera() {
    $consultaSql = "SELECT * FROM Cartelera";
    foreach (consulta($consultaSql) as $x) 
        $datos[] = array_merge(traerPelicula($x['idProducto']), $x);   
    return (!empty($datos)) ?
        $datos : null;
}

// Devuelve las categorías disponibles.
function traerCategorias() {
    $consultaSql = "SELECT * FROM Categorias";
    $datos = consulta($consultaSql);
    return (!empty($datos)) ?
        $datos : null;
}

// Devuelve las dimensiones disponibles.
function traerDimensiones() {
    $consultaSql = "SELECT * FROM Dimensiones";
    $datos = consulta($consultaSql);
    return (!empty($datos)) ?
        $datos : null;
}

// Devuelve los idiomas disponibles.
function traerIdiomas() {
    $consultaSql = "SELECT * FROM Idiomas";
    $datos = consulta($consultaSql);
    return (!empty($datos)) ?
        $datos : null;
}

function traerArticulos() {
    $consultaSql = "SELECT * FROM Articulo";
    $datos = consulta($consultaSql);
    return (!empty($datos)) ?
        $datos : null;
}


// Realiza la consulta requerida en la base de datos y devuelve un array que contiene los datos solicitados. Este método se utiliza cuando se traen todos los elementos de una tabla.
function consulta($consultaSql) {
    global $con;
    try {
        $statement = $con->prepare($consultaSql);
        $statement->execute();
        while ($fila = $statement->fetch(PDO::FETCH_ASSOC))
            $datos[] = $fila;
    } catch (PDOException $pe) {
        echo json_encode(['mensaje' => "Error en traer.php, consulta():" . $pe->getMessage()]);
        return null;
    }
    return (!empty($datos)) ?
        $datos : null;
}

// Realiza la consulta requerida en la base de datos y devuelve un array que contiene los datos solicitados. Este método se utiliza cuando se traen elementos de una tabla según un valor clave.
function consultaClave($consultaSql, $clave) {
    global $con;
    try {
        $statement = $con->prepare($consultaSql);
        $statement->execute($clave);
        while ($fila = $statement->fetch(PDO::FETCH_ASSOC))
            $datos[] = $fila;
    } catch (PDOException $pe) {
        echo json_encode(['mensaje' => "Error en traer.php, consultaClave():" . $pe->getMessage()]);
        return null;
    }
    return (!empty($datos)) ?
        $datos[0] : null;
}