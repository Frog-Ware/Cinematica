<?php

// Este script conecta a la base de datos mediante PDO.

$host = "localhost";
$dbname = "Cinematica";
$username = "root";
$password = "";

try {
    $con = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $pe) {
    die ("Could not connect to the database $dbname :" . $pe->getMessage());
}