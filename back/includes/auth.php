<?php
/**
 * Funciones de autenticación y control de acceso por rol.
 */

require_once __DIR__ . '/../config/conexion.php';

const ROL_ADMIN   = 'Administrador';
const ROL_USUARIO = 'Usuario';

// Rutas relativas a la carpeta front/
const RUTA_LOGIN     = 'login.php';
const RUTA_DASHBOARD = 'admin/dashboard.php';
const RUTA_USUARIO   = 'usuario/index.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * Busca el usuario por email y verifica la contraseña.
 * Devuelve los datos del usuario (con su rol) o null si las credenciales no son válidas.
 */
function verificarCredenciales(string $email, string $contrasena): ?array
{
    $sql = 'SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.contrasena, u.activo,
                   r.nombre AS rol
            FROM usuario u
            INNER JOIN rol r ON r.id_rol = u.id_rol
            WHERE u.email = :email
            LIMIT 1';

    $stmt = conectar()->prepare($sql);
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if (!$usuario || !$usuario['activo'] || !password_verify($contrasena, $usuario['contrasena'])) {
        return null;
    }

    return $usuario;
}

/** Guarda en la sesión los datos del usuario autenticado. */
function iniciarSesion(array $usuario): void
{
    session_regenerate_id(true); // evita fijación de sesión

    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre']     = $usuario['nombre'] . ' ' . $usuario['apellido'];
    $_SESSION['email']      = $usuario['email'];
    $_SESSION['rol']        = $usuario['rol'];
}

function cerrarSesion(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }

    session_destroy();
}

function estaLogueado(): bool
{
    return isset($_SESSION['id_usuario'], $_SESSION['rol']);
}

function esAdmin(): bool
{
    return estaLogueado() && $_SESSION['rol'] === ROL_ADMIN;
}

/** Devuelve la página a la que corresponde enviar al usuario según su rol. */
function rutaSegunRol(string $rol): string
{
    return $rol === ROL_ADMIN ? RUTA_DASHBOARD : RUTA_USUARIO;
}

/**
 * Redirige y termina la ejecución.
 * $base es el prefijo para llegar a front/ desde la página actual ('' o '../').
 */
function redirigir(string $ruta, string $base = ''): void
{
    header('Location: ' . $base . $ruta);
    exit;
}

/**
 * Protege una página: si no hay sesión se envía al login;
 * si el rol no es el requerido se envía a la sección que le corresponde.
 */
function requerirRol(string $rol, string $base = '../'): void
{
    if (!estaLogueado()) {
        redirigir(RUTA_LOGIN . '?error=acceso', $base);
    }

    if ($_SESSION['rol'] !== $rol) {
        redirigir(rutaSegunRol($_SESSION['rol']) . '?error=permiso', $base);
    }
}

/** Escapa texto para mostrarlo en HTML. */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

/** Token CSRF para los formularios. */
function tokenCsrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function validarCsrf(?string $token): bool
{
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
