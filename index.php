<?php
// Página de Login
session_start();
if (!empty($_SESSION['id_usuario'])) { header('Location: dashboard.php'); exit; }

require_once 'funciones.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = limpiar_datos($_POST['correo'] ?? '');
    $clave  = limpiar_datos($_POST['clave']  ?? '');

    if (empty($correo) || empty($clave)) {
        $error = 'Completa todos los campos.';
    } elseif (!str_ends_with($correo, '@uch.pe')) {
        $error = 'Usa tu correo institucional (@uch.pe).';
    } else {
        $usuario = autenticar_usuario($correo, $clave);
        if ($usuario) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre']     = $usuario['nombre'];
            $_SESSION['apellido']   = $usuario['apellido'];
            $_SESSION['ciclo']      = $usuario['ciclo'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Credenciales incorrectas.';
        }
    }
}
if (isset($_GET['error']) && $_GET['error'] === 'sesion') $error = 'Sesión expirada.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>INNOVA UCH - Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo_uch.css">
</head>
<body class="bg-crema d-flex align-items-center justify-content-center min-vh-100">
<main class="w-100 p-3" style="max-width:400px;">
    <div class="login-card shadow-lg p-4">
        <h3 class="text-center fw-bold text-marino mb-4">INNOVA UCH</h3>
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        
        <form method="POST">
            <input type="email" name="correo" class="form-control mb-3 input-uch" placeholder="usuario@uch.pe" required value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
            <input type="password" name="clave" class="form-control mb-3 input-uch" placeholder="Contraseña" required>
            <button type="submit" class="btn btn-uch w-100">Ingresar</button>
        </form>
        <p class="text-center mt-3 small"><a href="registro.php" class="link-uch">Crear cuenta</a></p>
    </div>
</main>
</body>
</html>
