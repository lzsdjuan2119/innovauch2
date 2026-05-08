<?php

    
// --- conexion.php ---

function obtener_conexion() {
    $host = getenv('MYSQLHOST')     ?: 'localhost';
    $port = getenv('MYSQLPORT')     ?: '3306';
    $user = getenv('MYSQLUSER')     ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: '';
    $db   = getenv('MYSQLDATABASE') ?: 'railway';

    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
        $conexion = new PDO($dsn, $user, $pass);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // ¡Esta es la parte importante! Devolvemos la conexión
        return $conexion; 
        
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        return null;
    }
}

// También dejamos la variable global por si algún archivo viejo la usa
$conexion = obtener_conexion();
?>
