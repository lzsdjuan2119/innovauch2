<?php
// Guarda nuevos proyectos
require_once 'funciones.php';
verificar_sesion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = limpiar_datos($_POST['titulo'] ?? '');
    $descripcion = limpiar_datos($_POST['descripcion'] ?? '');
    $categoria   = limpiar_datos($_POST['categoria'] ?? '');
    $id_usuario  = (int)$_SESSION['id_usuario'];

    if (guardar_proyecto($id_usuario, $titulo, $descripcion, $categoria)) {
        $_SESSION['msg'] = 'Proyecto registrado con éxito.';
    } else {
        $_SESSION['msg'] = 'Error al registrar idea.';
    }
}
header('Location: dashboard.php');
exit;