<?php
// Página de Registro
session_start();
if (!empty($_SESSION['id_usuario'])) { header('Location: dashboard.php'); exit; }
require_once 'funciones.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = limpiar_datos($_POST['nombre'] ?? '');
    $apellido = limpiar_datos($_POST['apellido'] ?? '');
    $correo   = limpiar_datos($_POST['correo'] ?? '');
    $clave    = limpiar_datos($_POST['clave'] ?? '');
    $ciclo    = (int)($_POST['ciclo'] ?? 1);

    if (empty($nombre) || empty($apellido) || empty($correo) || empty($clave)) {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!str_ends_with($correo, '@uch.pe')) {
        $error = 'El correo debe terminar en @uch.pe';
    } elseif ($ciclo < 1 || $ciclo > 10) {
        $error = 'Ciclo inválido.';
    } else {
        if (registrar_usuario($nombre, $apellido, $correo, $clave, $ciclo)) {
            header('Location: index.php?exito=1');
            exit;
        } else {
            $error = 'Error o correo ya registrado.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - INNOVA UCH</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo_uch.css">
</head>
<body class="bg-crema d-flex align-items-center justify-content-center min-vh-100">
<main class="w-100 p-3" style="max-width:500px;">
    <div class="login-card shadow-lg p-4">
        <h4 class="text-center text-marino fw-bold mb-3">Registro INNOVA</h4>
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        
        <form method="POST">
            <div class="row g-2 mb-2">
                <div class="col"><input type="text" name="nombre" class="form-control input-uch" placeholder="Nombre" required></div>
                <div class="col"><input type="text" name="apellido" class="form-control input-uch" placeholder="Apellido" required></div>
            </div>
            <input type="email" name="correo" class="form-control mb-2 input-uch" placeholder="usuario@uch.pe" required>
            <div class="row g-2 mb-3">
                <div class="col-8"><input type="password" name="clave" class="form-control input-uch" placeholder="Contraseña" required></div>
                <div class="col-4">
                    <select name="ciclo" class="form-select input-uch">
                        <?php for($i=1; $i<=10; $i++): ?><option value="<?= $i ?>"><?= $i ?>°</option><?php endfor; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-uch w-100">Crear Cuenta</button>
        </form>
        <p class="text-center mt-3 small"><a href="index.php" class="link-uch">Volver al login</a></p>
    </div>
</main>
</body>
</html>