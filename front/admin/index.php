<?php
// Acceso a /admin/ -> se envía al dashboard (que a su vez verifica el rol)
header('Location: dashboard.php');
exit;
