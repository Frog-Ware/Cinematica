<?php

// Este script conecta a la base de datos mediante PDO.

// Asigna los datos de ingreso a la BD en variables.
$host = "localhost";
$dbname = "Cinematica";
$username = "root";
$password = "";

/*
$host = "sql309.infinityfree.com";
$dbname = "if0_37383888_frogware";
$username = "if0_37383888";
$password = "ProyectoHosting";
*/

// Setea las opciones de PDO
$opc = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

// Intenta conectarse a la BD y de no lograrlo, mata la ejecución.
try {
    $con = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $opc);
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}