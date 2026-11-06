<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    // Configuración segura de la cookie de sesión
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Control de expiración por inactividad (Ejemplo: 15 minutos = 900 segundos)
$inactividad = 900;
if (isset($_SESSION['ultimo_acceso']) && (time() - $_SESSION['ultimo_acceso'] > $inactividad)) {
    session_unset();
    session_destroy();
    header('Location: login.php?mensaje=sesion_expirada');
    exit;
}
$_SESSION['ultimo_acceso'] = time();

// Función para restringir el acceso a usuarios no autenticados
function requerirAutenticacion() {
    if (!isset($_SESSION['estudiante_id'])) {
        header('Location: login.php');
        exit;
    }
}