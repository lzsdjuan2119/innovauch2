<?php
// dashboard.php
session_start();
require_once 'funciones.php';
verificar_sesion();

$id_usuario = $_SESSION['id_usuario'];
$nombre     = $_SESSION['nombre'];

// Consultas
$proyectos  = obtener_proyectos_con_autor();
$concursos  = obtener_concursos();
$mis_proy   = mis_proyectos($id_usuario);

$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>INNOVA UCH - Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo_uch.css">
</head>
<body class="bg-crema">

<header class="topbar-uch text-white p-3 d-flex justify-content-between align-items-center">
    <span class="fw-bold fs-5">INNOVA UCH</span>
    <div class="small">
        Hola, <strong><?= htmlspecialchars($nombre) ?></strong> | 
        <a href="cerrar_sesion.php" class="text-white text-decoration-none">Cerrar Sesión</a>
    </div>
</header>

<main class="container py-4">
    <?php if($msg): ?><div class="alert alert-info py-2 small"><?= $msg ?></div><?php endif; ?>

    <div class="row g-4">
        <div class="col-md-8">
            <h5 class="text-marino fw-bold mb-3">💡 Ideas en la Comunidad</h5>
            <div class="row g-3">
                <?php if(empty($proyectos)): ?>
                    <p class="text-muted small">No hay proyectos aún. ¡Sé el primero!</p>
                <?php else: ?>
                    <?php foreach($proyectos as $p): ?>
                    <div class="col-md-6">
                        <div class="proyecto-card h-100 <?= $p['id_usuario_fk'] == $id_usuario ? 'borde-mio' : '' ?>">
                            <span class="badge-cat <?= badge_categoria($p['categoria']) ?> mb-2"><?= $p['categoria'] ?></span>
                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($p['titulo']) ?></h6>
                            <p class="small text-muted flex-grow-1"><?= htmlspecialchars($p['descripcion']) ?></p>
                            <hr class="my-2 opacity-25">
                            <small class="text-secondary">Autor: <?= htmlspecialchars($p['nombre'] . " " . $p['apellido']) ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="login-card p-3 shadow-sm">
                <h6 class="text-marino fw-bold mb-3">🏆 Concursos Activos</h6>
                <?php if(empty($concursos)): ?>
                    <p class="text-muted small">No hay concursos disponibles.</p>
                <?php else: ?>
                    <?php foreach($concursos as $c): ?>
                        <div class="p-2 border rounded mb-2 bg-light">
                            <span class="d-block small fw-bold text-naranja"><?= htmlspecialchars($c['nombre_concurso']) ?></span>
                            <button class="btn btn-sm btn-outline-dark w-100 mt-2 py-1" data-bs-toggle="modal" data-bs-target="#modalConcurso<?= $c['id_concurso'] ?>">Inscribirme</button>
                        </div>

                        <div class="modal fade" id="modalConcurso<?= $c['id_concurso'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <form action="guardar_inscripcion.php" method="POST" class="modal-content">
                                    <div class="modal-body">
                                        <h6 class="fw-bold mb-3">Postular a <?= htmlspecialchars($c['nombre_concurso']) ?></h6>
                                        <input type="hidden" name="id_concurso" value="<?= $c['id_concurso'] ?>">
                                        <select name="id_proyecto" class="form-select form-select-sm" required>
                                            <option value="">-- Selecciona tu idea --</option>
                                            <?php foreach($mis_proy as $mp): ?>
                                                <option value="<?= $mp['id_proyecto'] ?>"><?= htmlspecialchars($mp['titulo']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="submit" class="btn btn-uch btn-sm w-100">Enviar Postulación</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<button class="fab" data-bs-toggle="modal" data-bs-target="#modalNuevo">+</button>

<div class="modal fade" id="modalNuevo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="guardar_proyecto.php" method="POST" class="modal-content p-3">
            <h5 class="fw-bold text-marino mb-3">Registrar Nueva Idea</h5>
            <input type="text" name="titulo" class="form-control mb-2 input-uch" placeholder="Título del proyecto" required maxlength="80">
            <textarea name="descripcion" class="form-control mb-2 input-uch" placeholder="Breve descripción..." required maxlength="200" rows="3"></textarea>
            <select name="categoria" class="form-select mb-3 input-uch" required>
                <option value="Tecnología">Tecnología</option>
                <option value="Salud">Salud</option>
                <option value="Educación">Educación</option>
                <option value="Medio Ambiente">Medio Ambiente</option>
            </select>
            <button type="submit" class="btn btn-uch w-100">Publicar Idea</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
