<?php
// config/database.php

$host = '127.0.0.1';
$port = '3307'; // Puerto configurado en XAMPP
$dbname = 'portal_asesorias';
$username = 'root'; // Usuario por defecto en XAMPP
$password = '';     // Contraseña por defecto en XAMPP

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Se oculta el mensaje interno del error en producción por seguridad
    die("Error al conectar con la base de datos.");
}