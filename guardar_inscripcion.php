<?php
// Procesa inscripciones a concursos
require_once 'funciones.php';
verificar_sesion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_proyecto = (int)($_POST['id_proyecto'] ?? 0);
    $id_concurso = (int)($_POST['id_concurso'] ?? 0);

    if ($id_proyecto > 0 && $id_concurso > 0) {
        if (inscribir_en_concurso($id_proyecto, $id_concurso)) {
            $_SESSION['msg'] = '¡Postulación enviada correctamente!';
        } else {
            $_SESSION['msg'] = 'Error o ya estás inscrito.';
        }
    }
}
header('Location: dashboard.php');
exit;