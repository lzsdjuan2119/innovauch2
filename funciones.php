<?php 
<?php
// funciones.php — VERSIÓN CORREGIDA

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

// ... resto de funciones igual ...

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

function registrar_usuario(string $nombre, string $apellido, string $correo, string $clave, int $ciclo): bool {
    $pdo = obtener_conexion();
    try {
        // Volvemos a usar el Procedimiento Almacenado
        $stmt = $pdo->prepare('CALL pa_insertar_usuario(?,?,?,?,?)');
        $stmt->execute([$nombre, $apellido, $correo, $clave, $ciclo]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
function autenticar_usuario(string $correo, string $clave): array|false {
    $pdo = obtener_conexion();
    try {
        // Volvemos a usar el Procedimiento Almacenado
        $stmt = $pdo->prepare('CALL pa_login(?)');
        $stmt->execute([$correo]);
        $fila = $stmt->fetch();

        if (!$fila) return false;
        
        // Compara en texto plano (sin BCRYPT, como solicitaste)
        if ($clave !== $fila['clave']) return false;
        
        return $fila;
    } catch (PDOException $e) {
        return false; // Evita el Error 500 si falla la BD
    }
}

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

function obtener_proyectos_con_autor(): array {
    $pdo = obtener_conexion();
    try {
        $sql = 'SELECT p.id_proyecto, p.titulo, p.descripcion, p.categoria, u.nombre, u.apellido, u.ciclo, p.id_usuario_fk
                FROM proyectos p
                INNER JOIN usuarios u ON u.id_usuario = p.id_usuario_fk
                ORDER BY p.id_proyecto DESC';
        return $pdo->query($sql)->fetchAll();
    } catch (PDOException $e) {
        return []; 
    }
}

// Funciones para concursos
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

function badge_categoria(string $categoria): string {
    return match ($categoria) {
        'Tecnología' => 'badge-tech',
        'Salud' => 'badge-salud',
        'Educación' => 'badge-edu',
        'Medio Ambiente' => 'badge-env',
        default => 'badge-otro',
    };
}
