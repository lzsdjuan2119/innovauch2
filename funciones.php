<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

if (session_status() === PHP_SESSION_NONE) session_start(); // ✅ sin duplicado

require_once 'conexion.php';

function limpiar_datos(string $datos): string {
    return htmlspecialchars(stripslashes(trim($datos)), ENT_QUOTES, 'UTF-8');
}

function verificar_sesion(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['id_usuario'])) {
        header('Location: index.php?error=sesion');
        exit;
    }
}

// ── Usuarios ────────────────────────────────────────────────

function registrar_usuario(
    string $nombre, string $apellido, string $correo,
    string $clave,  int $ciclo,       int $acepto_confidencial
): bool {
    $pdo = obtener_conexion();
    try {
        $stmt = $pdo->prepare('CALL pa_insertar_usuario(?,?,?,?,?,?)');
        $stmt->execute([$nombre, $apellido, $correo, $clave, $ciclo, $acepto_confidencial]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function autenticar_usuario(string $correo, string $clave): array|false {
    $pdo = obtener_conexion();
    try {
        $stmt = $pdo->prepare('CALL pa_login(?)');
        $stmt->execute([$correo]);
        $fila = $stmt->fetch();
        if (!$fila) return false;
        if ($clave !== $fila['clave']) return false;
        return $fila;
    } catch (PDOException $e) {
        return false;
    }
}

// ── Proyectos ───────────────────────────────────────────────

function guardar_proyecto(int $id_usuario, string $titulo, string $descripcion, string $categoria): bool {
    $pdo = obtener_conexion();
    try {
        $stmt = $pdo->prepare('INSERT INTO proyectos (id_usuario_fk, titulo, descripcion, categoria) VALUES (?,?,?,?)');
        $stmt->execute([$id_usuario, $titulo, $descripcion, $categoria]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function editar_proyecto(int $id_proyecto, int $id_usuario, string $titulo, string $descripcion, string $categoria): bool {
    $pdo = obtener_conexion();
    try {
        // La condición id_usuario_fk = ? evita que un usuario edite proyectos ajenos
        $stmt = $pdo->prepare(
            'UPDATE proyectos SET titulo=?, descripcion=?, categoria=?
             WHERE id_proyecto=? AND id_usuario_fk=?'
        );
        $stmt->execute([$titulo, $descripcion, $categoria, $id_proyecto, $id_usuario]);
        return $stmt->rowCount() > 0; // false si no era suyo
    } catch (PDOException $e) {
        return false;
    }
}

function eliminar_proyecto(int $id_proyecto, int $id_usuario): bool {
    $pdo = obtener_conexion();
    try {
        // Misma protección: solo borra si es el dueño
        $stmt = $pdo->prepare('DELETE FROM proyectos WHERE id_proyecto=? AND id_usuario_fk=?');
        $stmt->execute([$id_proyecto, $id_usuario]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

function obtener_proyecto_por_id(int $id_proyecto, int $id_usuario): array|false {
    $pdo = obtener_conexion();
    try {
        $stmt = $pdo->prepare(
            'SELECT id_proyecto, titulo, descripcion, categoria
             FROM proyectos WHERE id_proyecto=? AND id_usuario_fk=?'
        );
        $stmt->execute([$id_proyecto, $id_usuario]);
        return $stmt->fetch() ?: false;
    } catch (PDOException $e) {
        return false;
    }
}

function obtener_proyectos_con_autor(): array {
    $pdo = obtener_conexion();
    try {
        $sql = 'SELECT p.id_proyecto, p.titulo, p.descripcion, p.categoria,
                       u.nombre, u.apellido, u.ciclo, p.id_usuario_fk
                FROM proyectos p
                INNER JOIN usuarios u ON u.id_usuario = p.id_usuario_fk
                ORDER BY p.id_proyecto DESC';
        return $pdo->query($sql)->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function mis_proyectos(int $id_usuario): array {
    $pdo = obtener_conexion();
    try {
        $stmt = $pdo->prepare('SELECT id_proyecto, titulo FROM proyectos WHERE id_usuario_fk = ?');
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// ── Concursos ───────────────────────────────────────────────

function obtener_concursos(): array {
    $pdo = obtener_conexion();
    try {
        return $pdo->query("SELECT * FROM concursos WHERE estado = 'Activo'")->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function inscribir_en_concurso(int $id_proyecto, int $id_concurso): bool {
    $pdo = obtener_conexion();
    try {
        $stmt = $pdo->prepare('INSERT INTO inscripciones (id_proyecto_fk, id_concurso_fk) VALUES (?,?)');
        $stmt->execute([$id_proyecto, $id_concurso]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// ── UI helper ───────────────────────────────────────────────

function badge_categoria(string $categoria): string {
    return match ($categoria) {
        'Tecnología'    => 'badge-tech',
        'Salud'         => 'badge-salud',
        'Educación'     => 'badge-edu',
        'Medio Ambiente'=> 'badge-env',
        default         => 'badge-otro',
    };
}
