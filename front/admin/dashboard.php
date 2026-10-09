<?php
require_once __DIR__ . '/../../back/includes/auth.php';

// Seguridad: solo un Administrador con sesión iniciada puede ver esta página
requerirRol(ROL_ADMIN);

$db = conectar();

$totales = $db->query(
    'SELECT
        (SELECT COUNT(*) FROM articulo)  AS articulos,
        (SELECT COUNT(*) FROM categoria) AS categorias,
        (SELECT COUNT(*) FROM autor)     AS autores,
        (SELECT COUNT(*) FROM usuario)   AS usuarios'
)->fetch();

$articulos = $db->query(
    "SELECT a.id_articulo, a.titulo, a.precio, a.imagen, a.estado, a.destacado,
            c.nombre AS categoria,
            COALESCE(GROUP_CONCAT(CONCAT_WS(' ', au.nombre, au.apellido) SEPARATOR ', '), e.nombre) AS autor
     FROM articulo a
     INNER JOIN categoria c ON c.id_categoria = a.id_categoria
     LEFT JOIN editorial e ON e.id_editorial = a.id_editorial
     LEFT JOIN articulo_autor aa ON aa.id_articulo = a.id_articulo
     LEFT JOIN autor au ON au.id_autor = aa.id_autor
     GROUP BY a.id_articulo, a.titulo, a.precio, a.imagen, a.estado, a.destacado, c.nombre, e.nombre
     ORDER BY a.id_articulo"
)->fetchAll();

$aviso = ($_GET['error'] ?? '') === 'permiso' ? 'No tienes permisos para acceder a esa página.' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Biblioteca Virtual</title>
    <link rel="icon" type="image/icon" href="../dist/assets/biblioteca_icono-removebg-preview.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity=
    "sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../dist/css/admin.css">
</head>
<body class="admin">

    <!-- Barra superior -->
    <nav class="navbar navbar-dark bg-dark fixed-top admin-topbar">
        <div class="container-fluid flex-nowrap">
            <button class="btn btn-outline-light d-lg-none me-2" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#menuAdmin" aria-label="Abrir menú">
                &#9776;
            </button>
            <a class="navbar-brand me-auto text-truncate" href="dashboard.php">
                <img src="../dist/assets/biblioteca_icono-removebg-preview.png" alt="" style="width:28px;">
                <span class="d-none d-sm-inline">Panel de </span>Administración
            </a>
            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <span class="text-white-50 d-none d-sm-inline">
                    &#128100; <span class="text-white"><?= e($_SESSION['nombre']) ?></span>
                    <span class="badge bg-warning text-dark ms-1"><?= e($_SESSION['rol']) ?></span>
                </span>
                <a class="btn btn-warning btn-sm" href="../logout.php">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <!-- Menú lateral (offcanvas en móvil, fijo en escritorio) -->
    <aside class="offcanvas-lg offcanvas-start admin-sidebar bg-dark text-white" tabindex="-1" id="menuAdmin">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menú</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    data-bs-target="#menuAdmin" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-3">
            <p class="small text-white-50 d-sm-none mb-3">
                Conectado como <strong class="text-white"><?= e($_SESSION['nombre']) ?></strong>
            </p>
            <ul class="nav nav-pills flex-column gap-1">
                <li class="nav-item"><a class="nav-link active" href="#inicio">&#127968; Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="#articulos">&#128218; Artículos</a></li>
                <li class="nav-item"><a class="nav-link disabled" href="#" aria-disabled="true">&#128193; Categorías <small>(próx.)</small></a></li>
                <li class="nav-item"><a class="nav-link disabled" href="#" aria-disabled="true">&#9997; Autores <small>(próx.)</small></a></li>
                <li class="nav-item"><a class="nav-link disabled" href="#" aria-disabled="true">&#128101; Usuarios <small>(próx.)</small></a></li>
            </ul>
            <hr class="border-secondary">
            <ul class="nav nav-pills flex-column gap-1">
                <li class="nav-item"><a class="nav-link" href="../index.html">&#127760; Ver sitio público</a></li>
                <li class="nav-item"><a class="nav-link text-warning" href="../logout.php">&#128682; Cerrar sesión</a></li>
            </ul>
        </div>
    </aside>

    <main class="admin-content">
        <div class="container-fluid p-3 p-md-4">

            <?php if ($aviso): ?>
                <div class="alert alert-warning"><?= e($aviso) ?></div>
            <?php endif; ?>

            <section id="inicio" class="mb-4">
                <h1 class="h3 mb-1">Hola, <?= e($_SESSION['nombre']) ?></h1>
                <p class="text-muted">Bienvenido al panel de administración de la Biblioteca Virtual.</p>

                <div class="row g-3">
                    <div class="col-6 col-xl-3">
                        <div class="card stat-card border-start border-4 border-warning h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Artículos</p>
                                <p class="h3 mb-0"><?= (int) $totales['articulos'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="card stat-card border-start border-4 border-primary h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Categorías</p>
                                <p class="h3 mb-0"><?= (int) $totales['categorias'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="card stat-card border-start border-4 border-success h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Autores</p>
                                <p class="h3 mb-0"><?= (int) $totales['autores'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="card stat-card border-start border-4 border-danger h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Usuarios</p>
                                <p class="h3 mb-0"><?= (int) $totales['usuarios'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección destinada a la futura administración (ABML) de artículos -->
            <section id="articulos" class="card shadow-sm">
                <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h2 class="h5 mb-0">Gestión de artículos</h2>
                    <button class="btn btn-success btn-sm" type="button" disabled
                            title="Disponible en la próxima entrega">+ Agregar artículo</button>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Listado de artículos guardados en la base de datos. El alta, la modificación y la baja
                        de artículos se habilitarán en la próxima etapa del proyecto.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Portada</th>
                                    <th>Título</th>
                                    <th class="d-none d-md-table-cell">Autor / Editorial</th>
                                    <th>Categoría</th>
                                    <th class="text-end">Precio</th>
                                    <th class="d-none d-sm-table-cell">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($articulos as $a): ?>
                                    <tr>
                                        <td><?= (int) $a['id_articulo'] ?></td>
                                        <td><img src="../<?= e($a['imagen']) ?>" alt="" class="admin-thumb"></td>
                                        <td>
                                            <?= e($a['titulo']) ?>
                                            <?php if ($a['destacado']): ?>
                                                <span class="badge bg-warning text-dark">Más vendido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="d-none d-md-table-cell"><?= e($a['autor']) ?></td>
                                        <td><?= e($a['categoria']) ?></td>
                                        <td class="text-end">$<?= number_format((float) $a['precio'], 0, ',', '.') ?></td>
                                        <td class="d-none d-sm-table-cell">
                                            <span class="badge <?= $a['estado'] === 'Disponible' ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= e($a['estado']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <button class="btn btn-outline-primary btn-sm" disabled>Editar</button>
                                            <button class="btn btn-outline-danger btn-sm" disabled>Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity=
    "sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
