<?php
// dashboard.php - Innova UCH
session_start();

// --- 1. BYPASS DE VERIFICACIÓN ---
// Si por alguna razón no hay sesión (ej. entras directo al link), 
// creamos una para que la página no dé error de base de datos.
if (!isset($_SESSION['id_u'])) {
    $_SESSION['id_u'] = 1; 
    $_SESSION['nombre'] = 'Usuario Prueba';
}

// --- 2. CONEXIÓN A BASE DE DATOS ---
// Asegúrate de que conexion.php tenga las variables de Railway
require_once 'conexion.php'; 

$id_usuario = $_SESSION['id_u'];
$proyectos = [];

try {
    $db = obtener_conexion(); // Función que configuramos para Railway
    
    // Traemos los proyectos del usuario de la base de datos
    // Usamos una consulta simple para verificar que la DB responde
    $sql = "SELECT titulo, descripcion, estado FROM proyectos WHERE id_usuario = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id_usuario);
    $stmt->execute();
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si la base de datos falla, definimos un proyecto de error para que no se vea vacío
    $error_db = "Nota: No se pudo conectar a la DB de Railway: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Innova UCH</title>
    <link rel="stylesheet" href="estilo_uch.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f0f2f5; }
        .header { background: #004A99; color: white; padding: 1rem; display: flex; justify-content: space-between; }
        .container { padding: 20px; max-width: 1000px; margin: auto; }
        .card { background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .status { font-weight: bold; color: green; }
        .btn-logout { color: white; text-decoration: none; border: 1px solid white; padding: 5px 10px; border-radius: 4px; }
    </style>
</head>
<body>

    <div class="header">
        <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
        <a href="cerrar_sesion.php" class="btn-logout">Cerrar Sesión</a>
    </div>

    <div class="container">
        <h1>Mis Proyectos de Emprendimiento</h1>

        <?php if (isset($error_db)): ?>
            <div style="color: red; background: #ffe6e6; padding: 10px; margin-bottom: 20px;">
                <?php echo $error_db; ?>
            </div>
        <?php endif; ?>

        <div class="lista-proyectos">
            <?php if (count($proyectos) > 0): ?>
                <?php foreach ($proyectos as $p): ?>
                    <div class="card">
                        <h3><?php echo htmlspecialchars($p['titulo']); ?></h3>
                        <p><?php echo htmlspecialchars($p['descripcion']); ?></p>
                        <p>Estado: <span class="status"><?php echo htmlspecialchars($p['estado']); ?></span></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card">
                    <p>No tienes proyectos registrados en la base de datos.</p>
                    <button onclick="location.href='nuevo_proyecto.php'">+ Crear Primer Proyecto</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
