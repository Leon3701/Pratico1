<?php
require_once __DIR__ . '/../back/includes/auth.php';

// Si ya hay una sesión iniciada, se envía directamente a su sección
if (estaLogueado()) {
    redirigir(rutaSegunRol($_SESSION['rol']));
}

$error = '';
$email = '';

$mensajes = [
    'acceso' => 'Debes iniciar sesión para acceder a esa página.',
    'permiso' => 'No tienes permisos para acceder a esa página.',
];
$aviso = $mensajes[$_GET['error'] ?? ''] ?? '';
$salio = isset($_GET['logout']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email      = trim($_POST['email'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if (!validarCsrf($_POST['csrf'] ?? null)) {
        $error = 'La sesión del formulario expiró. Intenta nuevamente.';
    } elseif ($email === '' || $contrasena === '') {
        $error = 'Completa el email y la contraseña.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no tiene un formato válido.';
    } else {
        try {
            $usuario = verificarCredenciales($email, $contrasena);
        } catch (PDOException $ex) {
            $usuario = null;
            $error = 'No se pudo conectar con la base de datos.';
        }

        if ($usuario) {
            iniciarSesion($usuario);
            // Redirección según el rol: Administrador -> dashboard, Usuario -> sección de usuario
            redirigir(rutaSegunRol($usuario['rol']));
        } elseif ($error === '') {
            $error = 'Email o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Biblioteca Virtual</title>
    <link rel="icon" type="image/icon" href="dist/assets/biblioteca_icono-removebg-preview.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity=
    "sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="dist/css/login.css">
</head>
<body class="login-page">
    <header>
        <nav class="navbar bg-warning">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.html">
                    <img src="dist/assets/biblioteca_icono-removebg-preview.png" alt="" style="width:32px;"> Biblioteca Virtual
                </a>
                <a class="btn btn-outline-dark btn-sm" href="index.html">&larr; Volver al inicio</a>
            </div>
        </nav>
    </header>

    <main class="d-flex align-items-center justify-content-center py-5">
        <div class="card login-card shadow">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <img src="dist/assets/people-logo-design-free-vector-removebg-preview.png" alt="Usuario" class="login-logo">
                    <h1 class="h3 mt-2">Iniciar Sesión</h1>
                    <p class="text-muted mb-0">Ingresa con tu cuenta de la biblioteca</p>
                </div>

                <?php if ($salio): ?>
                    <div class="alert alert-success">Cerraste sesión correctamente.</div>
                <?php endif; ?>
                <?php if ($aviso): ?>
                    <div class="alert alert-warning"><?= e($aviso) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post" action="login.php" novalidate>
                    <input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= e($email) ?>" placeholder="nombre@ejemplo.com" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena"
                               placeholder="********" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container text-center">
            <p class="mb-0">&copy; <span id="year"></span> Biblioteca Virtual. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>document.getElementById("year").textContent = new Date().getFullYear();</script>
</body>
</html>
