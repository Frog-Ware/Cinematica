<?php

// Este script inserta en la base de datos los datos del usuario que necesitan de permanencia.

require ("conn.php");

// Agrega un usuario a la base de datos.
function nuevoUsuario($datos) {
    global $con;
    $sql_line = "INSERT INTO usuario(email, contraseña, nombre, apellido, numeroCelular, token) values (?, ?, ?, ?, ?, ?)";

    try{
        $statement = $con->prepare($sql_line);
        return $statement -> execute($datos);
    } catch (PDOException $pe) {
        die("no se pudo guardar la informacion:" . $pe->getMessage());
    }
}



?>