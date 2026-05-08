<?php
// index.php - Innova UCH (Modificado para acceso directo)
session_start();

// Si el usuario ya tiene sesión activa, lo mandamos al dashboard de una vez
if (isset($_SESSION['id_u'])) {
    header('Location: dashboard.php');
    exit;
}

// Procesar el clic en el botón "Ingresar"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Saltamos la validación de base de datos
    // 2. Creamos variables de sesión temporales (Datos de prueba)
    $_SESSION['id_u'] = 1; 
    $_SESSION['nombre'] = 'Usuario Invitado';
    $_SESSION['correo'] = $_POST['correo'] ?? 'invitado@uch.pe';
    $_SESSION['ciclo'] = 5;

    // 3. Redirección inmediata
    header('Location: dashboard.php');
    exit; // CRITICAL: Evita que el código siga ejecutándose en Railway
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Innova UCH - Login</title>
    <link rel="stylesheet" href="estilo_uch.css">
    <style>
        /* Estilo rápido para centrar el formulario */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 350px; text-align: center; }
        .logo { color: #004A99; font-weight: bold; font-size: 1.5rem; margin-bottom: 1.5rem; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #004A99; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #003366; }
        .footer-text { margin-top: 1rem; font-size: 0.8rem; color: #666; }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="logo">INNOVA UCH</div>
        <p>Bienvenido al Portal de Emprendimiento</p>
        
        <form method="POST" action="index.php">
            <input type="email" name="correo" placeholder="Correo institucional" required>
            <input type="password" name="clave" placeholder="Contraseña" required>
            
            <button type="submit">Ingresar</button>
        </form>

        <div class="footer-text">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>

</body>
</html>
