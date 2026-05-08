<?php
// 1. HOST (Servidor)
// Para Railway: Se llena solo con getenv. 
// TU MODIFICACIÓN: Si en tu PC (XAMPP) usas otro host, cambia 'localhost'.
$host = getenv('MYSQLHOST') ?: 'localhost'; 

// 2. PUERTO
// Para Railway: Se llena solo. 
// TU MODIFICACIÓN: Si en tu XAMPP el MySQL usa un puerto distinto (ej: 3307), cámbialo aquí.
$port = getenv('MYSQLPORT') ?: '3306';        

// 3. USUARIO
// Para Railway: Se llena solo. 
// TU MODIFICACIÓN: Si en tu XAMPP el usuario no es root, cámbialo aquí.
$user = getenv('MYSQLUSER') ?: 'root';        

// 4. CONTRASEÑA
// Para Railway: Se llena sola y se mantiene oculta. ¡NO PEGUES TU CLAVE DE RAILWAY AQUÍ!
// TU MODIFICACIÓN: Si tu XAMPP tiene contraseña, ponla dentro de las comillas simples ''.
$pass = getenv('MYSQLPASSWORD') ?: '';            

// 5. BASE DE DATOS
// Para Railway: Se llena solo (suele llamarse 'railway').
// TU MODIFICACIÓN: Pon el nombre exacto de tu base de datos en XAMPP.
$db   = getenv('MYSQLDATABASE') ?: 'innova_uch';  // <--- REVISA SI TU BD LOCAL SE LLAMA ASÍ
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    
    $conexion = new PDO($dsn, $user, $pass);
    
    // Configuración para que muestre los errores si falla
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Error crítico de conexión: " . $e->getMessage());
}
?>
