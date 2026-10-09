# Glosario de datos — Biblioteca Virtual

**Base de datos:** `biblioteca_virtual` · **Motor:** MySQL / MariaDB (InnoDB) · **Codificación:** utf8mb4

Referencias de la columna *Clave*: **PK** clave primaria · **FK** clave foránea · **UK** valor único.

---

## Tabla: `rol`

Roles que puede tener un usuario dentro del sistema.

| Campo       | Tipo de dato | Tamaño | Nulo | Clave | Valor por defecto | Descripción                                 |
|-------------|--------------|:------:|:----:|:-----:|-------------------|---------------------------------------------|
| id_rol      | INT          | —      | No   | PK    | AUTO_INCREMENT    | Identificador único del rol                 |
| nombre      | VARCHAR      | 30     | No   | UK    | —                 | Nombre del rol (Administrador, Usuario)     |
| descripcion | VARCHAR      | 150    | Sí   |       | NULL              | Descripción de los permisos del rol         |

## Tabla: `usuario`

Personas registradas que pueden iniciar sesión en el sitio.

| Campo          | Tipo de dato | Tamaño | Nulo | Clave | Valor por defecto  | Descripción                                               |
|----------------|--------------|:------:|:----:|:-----:|--------------------|-----------------------------------------------------------|
| id_usuario     | INT          | —      | No   | PK    | AUTO_INCREMENT     | Identificador único del usuario                           |
| nombre         | VARCHAR      | 50     | No   |       | —                  | Nombre del usuario                                        |
| apellido       | VARCHAR      | 50     | No   |       | —                  | Apellido del usuario                                      |
| email          | VARCHAR      | 100    | No   | UK    | —                  | Correo electrónico, se usa para iniciar sesión            |
| contrasena     | VARCHAR      | 255    | No   |       | —                  | Contraseña cifrada con `password_hash()` (bcrypt)         |
| fecha_registro | DATETIME     | —      | No   |       | CURRENT_TIMESTAMP  | Fecha y hora en que se creó la cuenta                     |
| activo         | TINYINT      | 1      | No   |       | 1                  | 1 = puede iniciar sesión, 0 = cuenta deshabilitada        |
| id_rol         | INT          | —      | No   | FK    | —                  | Rol del usuario → `rol.id_rol`                            |

## Tabla: `categoria`

Tipos de artículos que ofrece la biblioteca.

| Campo        | Tipo de dato | Tamaño | Nulo | Clave | Valor por defecto | Descripción                                   |
|--------------|--------------|:------:|:----:|:-----:|-------------------|-----------------------------------------------|
| id_categoria | INT          | —      | No   | PK    | AUTO_INCREMENT    | Identificador único de la categoría           |
| nombre       | VARCHAR      | 50     | No   | UK    | —                 | Nombre (Libro, Audiolibro, Revista)           |
| descripcion  | VARCHAR      | 150    | Sí   |       | NULL              | Descripción de la categoría                   |

## Tabla: `autor`

Autores de los libros y audiolibros.

| Campo        | Tipo de dato | Tamaño | Nulo | Clave | Valor por defecto | Descripción                              |
|--------------|--------------|:------:|:----:|:-----:|-------------------|------------------------------------------|
| id_autor     | INT          | —      | No   | PK    | AUTO_INCREMENT    | Identificador único del autor            |
| nombre       | VARCHAR      | 50     | No   |       | —                 | Nombre (o seudónimo) del autor           |
| apellido     | VARCHAR      | 50     | Sí   |       | NULL              | Apellido del autor                       |
| nacionalidad | VARCHAR      | 50     | Sí   |       | NULL              | País de origen del autor                 |

## Tabla: `editorial`

Editoriales o publicaciones (principalmente de las revistas).

| Campo        | Tipo de dato | Tamaño | Nulo | Clave | Valor por defecto | Descripción                                  |
|--------------|--------------|:------:|:----:|:-----:|-------------------|----------------------------------------------|
| id_editorial | INT          | —      | No   | PK    | AUTO_INCREMENT    | Identificador único de la editorial          |
| nombre       | VARCHAR      | 100    | No   | UK    | —                 | Nombre de la editorial o publicación         |
| pais         | VARCHAR      | 50     | Sí   |       | NULL              | País de la editorial                         |

## Tabla: `articulo`

Productos del catálogo: libros, audiolibros y revistas.

| Campo        | Tipo de dato | Tamaño | Nulo | Clave | Valor por defecto  | Descripción                                                    |
|--------------|--------------|:------:|:----:|:-----:|--------------------|----------------------------------------------------------------|
| id_articulo  | INT          | —      | No   | PK    | AUTO_INCREMENT     | Identificador único del artículo                               |
| titulo       | VARCHAR      | 150    | No   |       | —                  | Título del libro, audiolibro o revista                         |
| descripcion  | TEXT         | —      | Sí   |       | NULL               | Sinopsis o descripción del artículo                            |
| precio       | DECIMAL      | 10,2   | No   |       | —                  | Precio en pesos (debe ser ≥ 0)                                 |
| imagen       | VARCHAR      | 255    | No   |       | —                  | Ruta de la imagen de portada (ej. `dist/assets/libro-1.jpg`)   |
| estado       | ENUM         | —      | No   |       | 'Disponible'       | Disponibilidad: 'Disponible' o 'No disponible'                 |
| destacado    | TINYINT      | 1      | No   |       | 0                  | 1 = se muestra en "Más vendidos"                               |
| fecha_alta   | DATETIME     | —      | No   |       | CURRENT_TIMESTAMP  | Fecha en que se agregó al catálogo                             |
| id_categoria | INT          | —      | No   | FK    | —                  | Categoría del artículo → `categoria.id_categoria`              |
| id_editorial | INT          | —      | Sí   | FK    | NULL               | Editorial del artículo → `editorial.id_editorial`              |

## Tabla: `articulo_autor`

Tabla intermedia de la relación N:M entre artículos y autores.

| Campo       | Tipo de dato | Tamaño | Nulo | Clave   | Valor por defecto | Descripción                                 |
|-------------|--------------|:------:|:----:|:-------:|-------------------|---------------------------------------------|
| id_articulo | INT          | —      | No   | PK, FK  | —                 | Artículo → `articulo.id_articulo`           |
| id_autor    | INT          | —      | No   | PK, FK  | —                 | Autor del artículo → `autor.id_autor`       |

## Tabla: `favorito`

Artículos que cada usuario marcó como favoritos (botón ♥).

| Campo          | Tipo de dato | Tamaño | Nulo | Clave   | Valor por defecto | Descripción                                   |
|----------------|--------------|:------:|:----:|:-------:|-------------------|-----------------------------------------------|
| id_usuario     | INT          | —      | No   | PK, FK  | —                 | Usuario → `usuario.id_usuario`                |
| id_articulo    | INT          | —      | No   | PK, FK  | —                 | Artículo favorito → `articulo.id_articulo`    |
| fecha_agregado | DATETIME     | —      | No   |         | CURRENT_TIMESTAMP | Fecha en que se marcó como favorito           |
