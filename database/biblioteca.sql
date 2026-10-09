-- =====================================================================
--  Biblioteca Virtual - Práctico 2
--  Script de creación de la base de datos + datos de prueba
--  Motor: MySQL 8 / MariaDB 10.4+ (XAMPP)
-- =====================================================================

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS biblioteca_virtual;
CREATE DATABASE biblioteca_virtual
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE biblioteca_virtual;

-- ---------------------------------------------------------------------
--  ROL
-- ---------------------------------------------------------------------
CREATE TABLE rol (
    id_rol      INT          NOT NULL AUTO_INCREMENT,
    nombre      VARCHAR(30)  NOT NULL,
    descripcion VARCHAR(150) NULL,
    CONSTRAINT pk_rol PRIMARY KEY (id_rol),
    CONSTRAINT uq_rol_nombre UNIQUE (nombre)
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
--  USUARIO
-- ---------------------------------------------------------------------
CREATE TABLE usuario (
    id_usuario     INT          NOT NULL AUTO_INCREMENT,
    nombre         VARCHAR(50)  NOT NULL,
    apellido       VARCHAR(50)  NOT NULL,
    email          VARCHAR(100) NOT NULL,
    contrasena     VARCHAR(255) NOT NULL,           -- hash generado con password_hash()
    fecha_registro DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo         TINYINT(1)   NOT NULL DEFAULT 1,
    id_rol         INT          NOT NULL,
    CONSTRAINT pk_usuario PRIMARY KEY (id_usuario),
    CONSTRAINT uq_usuario_email UNIQUE (email),
    CONSTRAINT fk_usuario_rol FOREIGN KEY (id_rol)
        REFERENCES rol (id_rol)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
--  CATEGORIA  (Libro, Audiolibro, Revista)
-- ---------------------------------------------------------------------
CREATE TABLE categoria (
    id_categoria INT          NOT NULL AUTO_INCREMENT,
    nombre       VARCHAR(50)  NOT NULL,
    descripcion  VARCHAR(150) NULL,
    CONSTRAINT pk_categoria PRIMARY KEY (id_categoria),
    CONSTRAINT uq_categoria_nombre UNIQUE (nombre)
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
--  AUTOR
-- ---------------------------------------------------------------------
CREATE TABLE autor (
    id_autor     INT         NOT NULL AUTO_INCREMENT,
    nombre       VARCHAR(50) NOT NULL,
    apellido     VARCHAR(50) NULL,
    nacionalidad VARCHAR(50) NULL,
    CONSTRAINT pk_autor PRIMARY KEY (id_autor)
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
--  EDITORIAL
-- ---------------------------------------------------------------------
CREATE TABLE editorial (
    id_editorial INT          NOT NULL AUTO_INCREMENT,
    nombre       VARCHAR(100) NOT NULL,
    pais         VARCHAR(50)  NULL,
    CONSTRAINT pk_editorial PRIMARY KEY (id_editorial),
    CONSTRAINT uq_editorial_nombre UNIQUE (nombre)
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
--  ARTICULO  (libro, audiolibro o revista)
-- ---------------------------------------------------------------------
CREATE TABLE articulo (
    id_articulo  INT           NOT NULL AUTO_INCREMENT,
    titulo       VARCHAR(150)  NOT NULL,
    descripcion  TEXT          NULL,
    precio       DECIMAL(10,2) NOT NULL,
    imagen       VARCHAR(255)  NOT NULL,
    estado       ENUM('Disponible','No disponible') NOT NULL DEFAULT 'Disponible',
    destacado    TINYINT(1)    NOT NULL DEFAULT 0,  -- 1 = aparece en "Más vendidos"
    fecha_alta   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_categoria INT           NOT NULL,
    id_editorial INT           NULL,
    CONSTRAINT pk_articulo PRIMARY KEY (id_articulo),
    CONSTRAINT ck_articulo_precio CHECK (precio >= 0),
    CONSTRAINT fk_articulo_categoria FOREIGN KEY (id_categoria)
        REFERENCES categoria (id_categoria)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_articulo_editorial FOREIGN KEY (id_editorial)
        REFERENCES editorial (id_editorial)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
--  ARTICULO_AUTOR  (relación N:M entre ARTICULO y AUTOR)
-- ---------------------------------------------------------------------
CREATE TABLE articulo_autor (
    id_articulo INT NOT NULL,
    id_autor    INT NOT NULL,
    CONSTRAINT pk_articulo_autor PRIMARY KEY (id_articulo, id_autor),
    CONSTRAINT fk_aa_articulo FOREIGN KEY (id_articulo)
        REFERENCES articulo (id_articulo)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_aa_autor FOREIGN KEY (id_autor)
        REFERENCES autor (id_autor)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
--  FAVORITO  (relación N:M entre USUARIO y ARTICULO, botón ♥ de la landing)
-- ---------------------------------------------------------------------
CREATE TABLE favorito (
    id_usuario    INT      NOT NULL,
    id_articulo   INT      NOT NULL,
    fecha_agregado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_favorito PRIMARY KEY (id_usuario, id_articulo),
    CONSTRAINT fk_fav_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuario (id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_fav_articulo FOREIGN KEY (id_articulo)
        REFERENCES articulo (id_articulo)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;


-- =====================================================================
--  DATOS DE PRUEBA
-- =====================================================================

INSERT INTO rol (id_rol, nombre, descripcion) VALUES
    (1, 'Administrador', 'Acceso total al panel de administración'),
    (2, 'Usuario',       'Cliente / socio de la biblioteca');

-- Contraseñas de prueba (guardadas con password_hash):
--   admin@biblioteca.com   ->  Admin123!
--   usuario@biblioteca.com ->  Usuario123!
INSERT INTO usuario (nombre, apellido, email, contrasena, id_rol) VALUES
    ('Admin',  'Biblioteca', 'admin@biblioteca.com',
     '$2y$10$LO7r3rBT3crm8fRZwdIeg.CjKr4mRSL6CGVdguSlK2llNadr4xJO.', 1),
    ('Juan',   'Pérez',      'usuario@biblioteca.com',
     '$2y$10$QnUv7b4z0D7Gxs8i4nwPnOuWoBn332iij3bNcyv5tf6XkcVs/4/JC', 2);

INSERT INTO categoria (id_categoria, nombre, descripcion) VALUES
    (1, 'Libro',      'Libros en formato digital'),
    (2, 'Audiolibro', 'Libros narrados en formato de audio'),
    (3, 'Revista',    'Revistas de actualidad, deporte y entretenimiento');

INSERT INTO editorial (id_editorial, nombre, pais) VALUES
    (1, 'VOGUE',         'Estados Unidos'),
    (2, 'Vanity Fair',   'Estados Unidos'),
    (3, 'Rolling Stone', 'Estados Unidos'),
    (4, 'BBC',           'Reino Unido'),
    (5, 'SPORT',         'España'),
    (6, 'MARCA',         'España');

INSERT INTO autor (id_autor, nombre, apellido) VALUES
    (1,  'Adam',      'Silvera'),
    (2,  'Charles',   'Dickens'),
    (3,  'Ray',       'Bradbury'),
    (4,  'Vee',       NULL),
    (5,  'Herman',    'Melville'),
    (6,  'Aiden',     'Thomas'),
    (7,  'George',    'Orwell'),
    (8,  'A. M.',     'Strickland'),
    (9,  'Price',     'Ainsworth'),
    (10, 'James',     'Dashner'),
    (11, 'C. S.',     'Lewis'),
    (12, 'Dot',       'Hutchison'),
    (13, 'Agustina',  'Bazterrica'),
    (14, 'Franz',     'Kafka'),
    (15, 'Antoine',   'de Saint-Exupéry'),
    (16, 'Miguel',    'de Cervantes');

-- Los mismos artículos que se muestran en la landing page del Práctico 1
INSERT INTO articulo (id_articulo, titulo, precio, imagen, destacado, id_categoria, id_editorial) VALUES
    -- Más vendidos (libros destacados)
    (1,  'They both die at the end',     799.00, 'dist/assets/libro-1.jpg',  1, 1, NULL),
    (2,  'Great expectations',           799.00, 'dist/assets/libro-2.jpg',  1, 1, NULL),
    (3,  'Fahrenheit 451',               799.00, 'dist/assets/libro-3.jpg',  1, 1, NULL),
    (4,  'A tale of two cities',         799.00, 'dist/assets/libro-4.jpg',  1, 1, NULL),
    (5,  'Late night thoughts',          799.00, 'dist/assets/libro-5.jpg',  1, 1, NULL),
    (6,  'Moby Dick',                    799.00, 'dist/assets/libro-6.jpg',  1, 1, NULL),
    -- Libros
    (7,  'Lost in the Never Woods',      899.00, 'dist/assets/libro-7.jpg',  0, 1, NULL),
    (8,  '1984',                         899.00, 'dist/assets/libro-8.jpg',  0, 1, NULL),
    (9,  'Animal farm',                  899.00, 'dist/assets/libro-9.jpg',  0, 1, NULL),
    (10, 'Beyond the black door',        899.00, 'dist/assets/libro-10.jpg', 0, 1, NULL),
    (11, 'A minor fall',                 899.00, 'dist/assets/libro-11.jpg', 0, 1, NULL),
    (12, 'The maze runner',              899.00, 'dist/assets/libro-12.jpg', 0, 1, NULL),
    -- Audiolibros
    (13, 'The chronicles of Narnia',     999.00, 'dist/assets/libro-13.jpg', 0, 2, NULL),
    (14, 'El jardín de las mariposas',   999.00, 'dist/assets/libro-14.jpg', 0, 2, NULL),
    (15, 'Cadáver exquisito',            999.00, 'dist/assets/libro-15.jpg', 0, 2, NULL),
    (16, 'La metamorfosis',              999.00, 'dist/assets/libro-16.jpg', 0, 2, NULL),
    (17, 'El principito',                999.00, 'dist/assets/libro-17.jpg', 0, 2, NULL),
    (18, 'Don Quijote de la Mancha',     999.00, 'dist/assets/libro-18.jpg', 0, 2, NULL),
    -- Revistas
    (19, 'Margot Robbie',               1299.00, 'dist/assets/mag-1.jpg',    0, 3, 1),
    (20, 'Robert Downey Jr.',           1299.00, 'dist/assets/mag-2.jpg',    0, 3, 2),
    (21, 'Michael Jackson',             1299.00, 'dist/assets/mag-3.jpg',    0, 3, 3),
    (22, 'Doctor Who',                  1299.00, 'dist/assets/mag-4.jpg',    0, 3, 4),
    (23, 'Lionel Messi',                1299.00, 'dist/assets/mag-5.jpg',    0, 3, 5),
    (24, 'Cristiano Ronaldo',           1299.00, 'dist/assets/mag-6.jpg',    0, 3, 6);

INSERT INTO articulo_autor (id_articulo, id_autor) VALUES
    (1, 1), (2, 2), (3, 3), (4, 2), (5, 4), (6, 5),
    (7, 6), (8, 7), (9, 7), (10, 8), (11, 9), (12, 10),
    (13, 11), (14, 12), (15, 13), (16, 14), (17, 15), (18, 16);

INSERT INTO favorito (id_usuario, id_articulo) VALUES
    (2, 8), (2, 17);
