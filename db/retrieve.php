<?php

// Este script devuelve los datos requeridos por diferentes scripts.

require ("conn.php");

// Devuelve la contraseña asociada al usuario poseedor del email ingresado.
function traerContraseña($email){
    $consultaSql = "SELECT email, contraseña FROM Usuario where email = ?";
    $datos = consulta($consultaSql, $email);
    if (!empty($datos) && isset($datos['contraseña'])) {
        return $datos['contraseña'];
    } else {
        return null;
    }
}

// Devuelve los datos asociados al usuario poseedor del email ingresado.
function traerUsuario($email){
    $consultaSql = "SELECT email, nombre, apellido, imagenPerfil, contraseña FROM Usuario where email = ?";
    return consulta($consultaSql, $email);
}

// Realiza la consulta requerida en la base de datos y devuelve un array que contiene los datos solicitados.
function consulta($consultaSql, $clave){
    global $con;
    $datos = [];
    try {
        $statement = $con->prepare($consultaSql);
        $statement->execute([$clave]);
        while ($fila = $statement->fetch(PDO::FETCH_ASSOC)) {
            $datos[] = $fila;
        }
    } catch (PDOException $pe) {
        die("no se pudo guardar la informacion:" . $pe->getMessage());
    }
    return $datos[0];
}

?>