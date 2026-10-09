# Biblioteca Virtual

Proyecto incremental de la materia. Landing page de una biblioteca virtual (libros, audiolibros y revistas) que en el **Práctico 2** incorpora base de datos, inicio de sesión con roles y un panel de administración.

**Tecnologías:** HTML5 · CSS3 · JavaScript · Bootstrap 5 · PHP 8 (PDO) · MySQL / MariaDB

## Práctico 2 — Dónde encontrar cada punto

| # | Requisito                                 | Ubicación                                                                 |
|---|-------------------------------------------|---------------------------------------------------------------------------|
| 1 | MER                                       | [`docs/MER.md`](docs/MER.md)                                              |
| 2 | Pasaje a tablas                           | [`docs/pasaje_a_tablas.md`](docs/pasaje_a_tablas.md)                      |
| 3 | Glosario de datos                         | [`docs/glosario_datos.md`](docs/glosario_datos.md)                        |
| 4 | Base de datos implementada                | [`database/biblioteca.sql`](database/biblioteca.sql)                      |
| 5 | Login                                     | [`front/login.php`](front/login.php) · [`back/includes/auth.php`](back/includes/auth.php) |
| 6 | Diferenciación usuario / administrador    | Tabla `rol` + `$_SESSION['rol']` (función `rutaSegunRol()`)              |
| 7 | Dashboard del administrador               | [`front/admin/dashboard.php`](front/admin/dashboard.php)                  |
| 8 | Redirección según el rol                  | Administrador → `admin/dashboard.php` · Usuario → `usuario/index.php`     |
| 9 | Protección del acceso administrativo      | `requerirRol(ROL_ADMIN)` al inicio del dashboard                          |

## Estructura

```
├── index.php               Redirige a front/
├── front/                  Sitio público
│   ├── index.html          Landing page (Práctico 1)
│   ├── login.php           Formulario de inicio de sesión
│   ├── logout.php          Cierra la sesión
│   ├── admin/dashboard.php Panel del administrador (protegido)
│   ├── usuario/index.php   Sección del usuario (protegida)
│   └── dist/               css, js y assets
├── back/                   Lógica del servidor (no accesible desde el navegador)
│   ├── config/conexion.php Conexión PDO a MySQL
│   └── includes/auth.php   Login, sesión, roles y protección de rutas
├── database/biblioteca.sql Creación de tablas + datos de prueba
└── docs/                   MER, pasaje a tablas y glosario de datos
```

## Cómo ejecutarlo

### Opción A — XAMPP

1. Copiar la carpeta del proyecto dentro de `C:\xampp\htdocs\` (por ejemplo `htdocs\Pratico1`).
2. Iniciar **Apache** y **MySQL** desde el panel de XAMPP.
3. Entrar a <http://localhost/phpmyadmin>, pestaña **Importar**, y elegir `database/biblioteca.sql`.
4. Abrir <http://localhost/Pratico1/>.

La conexión usa por defecto `localhost`, usuario `root` y sin contraseña (valores de XAMPP). Si tu MySQL usa otros datos, se cambian en `back/config/conexion.php`.

### Opción B — Docker

```bash
docker compose up -d
```

- Sitio: <http://localhost:8080>
- phpMyAdmin: <http://localhost:8081> (usuario `root`, contraseña `root`)

La base de datos se crea sola la primera vez a partir de `database/biblioteca.sql`.

## Usuarios de prueba

| Rol           | Email                    | Contraseña    | Después del login va a…  |
|---------------|--------------------------|---------------|--------------------------|
| Administrador | `admin@biblioteca.com`   | `Admin123!`   | `admin/dashboard.php`    |
| Usuario       | `usuario@biblioteca.com` | `Usuario123!` | `usuario/index.php`      |

## Seguridad implementada

- Contraseñas guardadas con `password_hash()` y verificadas con `password_verify()`.
- Consultas preparadas (PDO) para evitar inyección SQL.
- `session_regenerate_id()` al iniciar sesión y token CSRF en el formulario de login.
- Las páginas protegidas llaman a `requerirRol()`:
  - sin sesión → se redirige a `login.php`;
  - con sesión pero sin el rol correcto (ej. un Usuario que escribe la URL del dashboard) → se redirige a su propia sección.
- Las carpetas `back/` y `database/` bloquean el acceso directo desde el navegador (`.htaccess`).
