<?php

// Este script conecta a la base de datos mediante PDO.

// Asigna los datos de ingreso a la BD en variables.
$host = "localhost";
$dbname = "Cinematica";
$username = "root";
$password = "";

// Intenta conectarse a la BD y de no lograrlo, mata la ejecución.
try {
    $con = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $pe) {
    die ("Could not connect to the database $dbname :" . $pe->getMessage());
}