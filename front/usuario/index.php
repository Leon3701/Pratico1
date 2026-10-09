<?php
require_once __DIR__ . '/../../back/includes/auth.php';

// Sección destinada a los usuarios con rol "Usuario".
// En este práctico solo se demuestra la redirección; la página se desarrollará más adelante.
requerirRol(ROL_USUARIO);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi cuenta | Biblioteca Virtual</title>
    <link rel="icon" type="image/icon" href="../dist/assets/biblioteca_icono-removebg-preview.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity=
    "sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body style="background-color:#ffe4c4;">
    <nav class="navbar bg-warning">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.html">Biblioteca Virtual</a>
            <a class="btn btn-outline-dark btn-sm" href="../logout.php">Cerrar sesión</a>
        </div>
    </nav>

    <main class="container py-5 text-center">
        <?php if (($_GET['error'] ?? '') === 'permiso'): ?>
            <div class="alert alert-warning">No tienes permisos de administrador para acceder a esa página.</div>
        <?php endif; ?>

        <h1 class="h3">Hola, <?= e($_SESSION['nombre']) ?></h1>
        <p>Iniciaste sesión con rol <strong><?= e($_SESSION['rol']) ?></strong>.</p>
        <p class="text-muted">La sección de usuario está en construcción.</p>
        <a class="btn btn-primary" href="../index.html">Ir al catálogo</a>
    </main>
</body>
</html>
