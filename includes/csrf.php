<?php
// includes/csrf.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Genera un token CSRF único si no existe
function obtenerTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verifica el token recibido desde el formulario
function verificarTokenCSRF($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Solicitud rechazada: Token CSRF no válido.');
    }
}