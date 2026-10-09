<?php
require_once __DIR__ . '/../back/includes/auth.php';

cerrarSesion();
redirigir(RUTA_LOGIN . '?logout=1');
