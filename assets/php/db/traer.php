<?php

// Este script devuelve los datos requeridos por diferentes scripts.

require ("conn.php");

// Devuelve la contraseña asociada al usuario poseedor del email ingresado.
function traerPasswd($email) {
    $consultaSql = "SELECT passwd FROM Usuario where email = ?";
    $datos = consultaUnica($consultaSql, $email);
    return (!empty($datos) && isset($datos['passwd'])) ? $datos['passwd'] : null;
}

// Devuelve los datos asociados al usuario poseedor del email ingresado.
function traerUsuario($email) {
    $consultaSql = "SELECT email, nombre, apellido, imagenPerfil, passwd FROM Usuario where email = ?";
    $datos = consultaUnica($consultaSql, $email);
    return (!empty($datos)) ? $datos : null;
}

// Devuelve la pelicula asociada al nombre.
function traerPelicula($id) {
    $consultaSql = "SELECT * FROM Pelicula where idProducto = ?";
    $datos = consultaUnica($consultaSql, $id);
    return (!empty($datos)) ? $datos : null;
}

// Devuelve la cartelera entera.
function traerCartelera() {
    $consultaSql = "SELECT idProducto FROM Cartelera";
    foreach (consulta($consultaSql) as $x) $datos[] = traerPelicula($x['idProducto']);
    return (!empty($datos)) ? $datos : null;
}

// Devuelve las categorías disponibles.
function traerCategorias() {
    $consultaSql = "SELECT * FROM Categorias";
    $datos = consulta($consultaSql);
    return (!empty($datos)) ? $datos : null;
}

// Devuelve las dimensiones disponibles.
function traerDimensiones() {
    $consultaSql = "SELECT * FROM Dimensiones";
    $datos = consulta($consultaSql);
    return (!empty($datos)) ? $datos : null;
}

// Devuelve los idiomas disponibles.
function traerIdiomas() {
    $consultaSql = "SELECT * FROM Idiomas";
    $datos = consulta($consultaSql);
    return (!empty($datos)) ? $datos : null;
}



// Realiza la consulta requerida en la base de datos y devuelve un array que contiene los datos solicitados.
function consulta($consultaSql) {
    global $con;
    try {
        $statement = $con->prepare($consultaSql);
        $statement->execute();
        while ($fila = $statement->fetch(PDO::FETCH_ASSOC)) $datos[] = $fila;
    } catch (PDOException $pe) {
        echo json_encode(['mensaje' => "Error en traer.php, consulta():" . $pe->getMessage()]);
        return null;
    }
    return (!empty($datos)) ? $datos : null;
}

function consultaUnica($consultaSql, $clave) {
    global $con;
    try {
        $statement = $con->prepare($consultaSql);
        $statement->execute([$clave]);
        while ($fila = $statement->fetch(PDO::FETCH_ASSOC)) {
            $datos[] = $fila;
        }
    } catch (PDOException $pe) {
        echo json_encode(['mensaje' => "Error en traer.php, consultaUnica():" . $pe->getMessage()]);
        return null;
    }
    return (!empty($datos)) ? $datos[0] : null;
}

?>