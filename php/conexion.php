<?php

$dsn = 'mysql:host=localhost;dbname=bd_restaurante2;charset=utf8';
$username = 'root';
$password = '';

try {
    $conexion = new PDO($dsn, $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . htmlspecialchars($e->getMessage());
}