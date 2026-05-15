<?php
session_start();
require_once 'funciones.php';
verificar_sesion();

$id_usuario = $_SESSION['id_usuario'];
$nombre     = $_SESSION['nombre'];

$proyectos = obtener_proyectos_con_autor();
$concursos = obtener_concursos();
$mis_proy  = mis_proyectos($id_usuario);

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
    <style>
        .btn-card-action {
            border: none; background: transparent; padding: 4px 7px;
            border-radius: 6px; cursor: pointer; transition: background .15s;
            line-height: 1;
        }
        .btn-card-action:hover { background: #f1f5f9; }
        .btn-card-action.editar  { color: #2563EB; }
        .btn-card-action.borrar  { color: #DC2626; }
        .card-acciones { display: flex; gap: 4px; }
    </style>
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

    <?php if ($msg): ?>
        <div class="alert alert-info py-2 small alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- ── Proyectos de la comunidad ── -->
        <div class="col-md-8">
            <h5 class="text-marino fw-bold mb-3">💡 Ideas en la Comunidad</h5>
            <div class="row g-3">
                <?php if (empty($proyectos)): ?>
                    <p class="text-muted small">No hay proyectos aún. ¡Sé el primero!</p>
                <?php else: ?>
                    <?php foreach ($proyectos as $p): ?>
                    <?php $es_mio = ($p['id_usuario_fk'] == $id_usuario); ?>
                    <div class="col-md-6">
                        <div class="proyecto-card h-100 <?= $es_mio ? 'borde-mio' : '' ?>">

                            <!-- Cabecera badge + botones (solo si es mío) -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge-cat <?= badge_categoria($p['categoria']) ?>">
                                    <?= htmlspecialchars($p['categoria']) ?>
                                </span>
                                <?php if ($es_mio): ?>
                                <div class="card-acciones">
                                    <!-- Botón Editar -->
                                    <button class="btn-card-action editar"
                                            title="Editar proyecto"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditar<?= $p['id_proyecto'] ?>">
                                        ✏️
                                    </button>
                                    <!-- Botón Eliminar -->
                                    <button class="btn-card-action borrar"
                                            title="Eliminar proyecto"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEliminar<?= $p['id_proyecto'] ?>">
                                        🗑️
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>

                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($p['titulo']) ?></h6>
                            <p class="small text-muted flex-grow-1"><?= htmlspecialchars($p['descripcion']) ?></p>
                            <hr class="my-2 opacity-25">
                            <small class="text-secondary">
                                Autor: <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?>
                            </small>
                        </div>
                    </div>

                    <?php if ($es_mio): ?>

                    <!-- Modal EDITAR -->
                    <div class="modal fade" id="modalEditar<?= $p['id_proyecto'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <form action="editar_proyecto.php" method="POST" class="modal-content p-3">
                                <h5 class="fw-bold text-marino mb-3">✏️ Editar Proyecto</h5>
                                <input type="hidden" name="id_proyecto" value="<?= $p['id_proyecto'] ?>">
                                <input type="text" name="titulo"
                                       class="form-control mb-2 input-uch"
                                       value="<?= htmlspecialchars($p['titulo']) ?>"
                                       placeholder="Título" required maxlength="80">
                                <textarea name="descripcion"
                                          class="form-control mb-2 input-uch"
                                          placeholder="Descripción" required maxlength="200"
                                          rows="3"><?= htmlspecialchars($p['descripcion']) ?></textarea>
                                <select name="categoria" class="form-select mb-3 input-uch" required>
                                    <?php foreach (['Tecnología','Salud','Educación','Medio Ambiente'] as $cat): ?>
                                        <option value="<?= $cat ?>" <?= $p['categoria']===$cat?'selected':'' ?>>
                                            <?= $cat ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary w-50"
                                            data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-uch w-50">Guardar cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal ELIMINAR (confirmación) -->
                    <div class="modal fade" id="modalEliminar<?= $p['id_proyecto'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <div class="modal-content p-3 text-center">
                                <p class="fs-5 mb-1">🗑️</p>
                                <h6 class="fw-bold mb-1">¿Eliminar proyecto?</h6>
                                <p class="small text-muted mb-3">
                                    «<?= htmlspecialchars($p['titulo']) ?>» se borrará permanentemente.
                                </p>
                                <form action="eliminar_proyecto.php" method="POST">
                                    <input type="hidden" name="id_proyecto" value="<?= $p['id_proyecto'] ?>">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-secondary w-50 btn-sm"
                                                data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit"
                                                class="btn btn-sm w-50"
                                                style="background:#DC2626;color:#fff;border-radius:10px;">
                                            Sí, eliminar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Concursos activos ── -->
        <div class="col-md-4">
            <div class="login-card p-3 shadow-sm">
                <h6 class="text-marino fw-bold mb-3">🏆 Concursos Activos</h6>
                <?php if (empty($concursos)): ?>
                    <p class="text-muted small">No hay concursos disponibles.</p>
                <?php else: ?>
                    <?php foreach ($concursos as $c): ?>
                    <div class="p-2 border rounded mb-2 bg-light">
                        <span class="d-block small fw-bold text-naranja">
                            <?= htmlspecialchars($c['nombre_concurso']) ?>
                        </span>
                        <button class="btn btn-sm btn-outline-dark w-100 mt-2 py-1"
                                data-bs-toggle="modal"
                                data-bs-target="#modalConcurso<?= $c['id_concurso'] ?>">
                            Inscribirme
                        </button>
                    </div>

                    <!-- Modal inscripción concurso -->
                    <div class="modal fade" id="modalConcurso<?= $c['id_concurso'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <form action="guardar_inscripcion.php" method="POST" class="modal-content">
                                <div class="modal-body">
                                    <h6 class="fw-bold mb-3">
                                        Postular a <?= htmlspecialchars($c['nombre_concurso']) ?>
                                    </h6>
                                    <input type="hidden" name="id_concurso" value="<?= $c['id_concurso'] ?>">
                                    <select name="id_proyecto" class="form-select form-select-sm" required>
                                        <option value="">-- Selecciona tu idea --</option>
                                        <?php foreach ($mis_proy as $mp): ?>
                                            <option value="<?= $mp['id_proyecto'] ?>">
                                                <?= htmlspecialchars($mp['titulo']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (empty($mis_proy)): ?>
                                        <p class="small text-danger mt-2 mb-0">
                                            Primero registra un proyecto con el botón +
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="submit" class="btn btn-uch btn-sm w-100"
                                            <?= empty($mis_proy) ? 'disabled' : '' ?>>
                                        Enviar Postulación
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- /row -->
</main>

<!-- FAB nuevo proyecto -->
<button class="fab" data-bs-toggle="modal" data-bs-target="#modalNuevo" title="Nueva idea">+</button>

<!-- Modal nuevo proyecto -->
<div class="modal fade" id="modalNuevo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="guardar_proyecto.php" method="POST" class="modal-content p-3">
            <h5 class="fw-bold text-marino mb-3">Registrar Nueva Idea</h5>
            <input type="text" name="titulo" class="form-control mb-2 input-uch"
                   placeholder="Título del proyecto" required maxlength="80">
            <textarea name="descripcion" class="form-control mb-2 input-uch"
                      placeholder="Breve descripción..." required maxlength="200" rows="3"></textarea>
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
