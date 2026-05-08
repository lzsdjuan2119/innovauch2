
CREATE DATABASE IF NOT EXISTS innova_uch;
USE innova_uch;

-- Tabla usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario      INT          NOT NULL AUTO_INCREMENT,
    nombre          VARCHAR(40)  NOT NULL,
    apellido        VARCHAR(40)  NOT NULL,
    correo          VARCHAR(50)  NOT NULL UNIQUE,
    clave           VARCHAR(255) NOT NULL,
    ciclo           INT          NOT NULL DEFAULT 1,
    PRIMARY KEY (id_usuario)
);

-- Tabla proyectos
CREATE TABLE IF NOT EXISTS proyectos (
    id_proyecto     INT          NOT NULL AUTO_INCREMENT,
    id_usuario_fk   INT          NOT NULL,
    titulo          VARCHAR(80)  NOT NULL,
    descripcion     VARCHAR(200) NOT NULL,
    categoria       VARCHAR(25)  NOT NULL,
    PRIMARY KEY (id_proyecto),
    CONSTRAINT fk_usuario
        FOREIGN KEY (id_usuario_fk) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
);

-- Tabla concursos (NUEVO)
CREATE TABLE IF NOT EXISTS concursos (
    id_concurso     INT          NOT NULL AUTO_INCREMENT,
    nombre_concurso VARCHAR(100) NOT NULL,
    estado          VARCHAR(20)  DEFAULT 'Activo',
    PRIMARY KEY (id_concurso)
);

-- Tabla inscripciones (NUEVO)
CREATE TABLE IF NOT EXISTS inscripciones (
    id_inscripcion  INT NOT NULL AUTO_INCREMENT,
    id_proyecto_fk  INT NOT NULL,
    id_concurso_fk  INT NOT NULL,
    PRIMARY KEY (id_inscripcion),
    CONSTRAINT fk_proyecto FOREIGN KEY (id_proyecto_fk) REFERENCES proyectos(id_proyecto) ON DELETE CASCADE,
    CONSTRAINT fk_concurso FOREIGN KEY (id_concurso_fk) REFERENCES concursos(id_concurso) ON DELETE CASCADE
);

-- Procedimiento: Insertar Usuario
DELIMITER //
CREATE PROCEDURE pa_inserqtar_usuario (
    IN p_nombre    VARCHAR(40),
    IN p_apellido  VARCHAR(40),
    IN p_correo    VARCHAR(50),
    IN p_clave     VARCHAR(255),
    IN p_ciclo     INT
)
BEGIN
    INSERT INTO usuarios (nombre, apellido, correo, clave, ciclo)
    VALUES (p_nombre, p_apellido, p_correo, p_clave, p_ciclo);
END //
DELIMITER ;

-- Procedimiento: Login
DELIMITER //
CREATE PROCEDURE pa_login (
    IN p_correo VARCHAR(50)
)
BEGIN
    SELECT id_usuario, nombre, apellido, correo, clave, ciclo
    FROM   usuarios
    WHERE  correo = p_correo
    LIMIT  1;
END //
DELIMITER ;

-- Datos de prueba
INSERT IGNORE INTO usuarios (nombre, apellido, correo, clave, ciclo) 
VALUES ('Alumno', 'Prueba', 'prueba@uch.pe', '123456', 4);

INSERT IGNORE INTO concursos (nombre_concurso, estado) 
VALUES ('Feria Tecnológica 2026', 'Activo'), ('Hackathon Innova', 'Activo');