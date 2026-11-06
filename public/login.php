<?php
// public/login.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

$error = '';
$mensaje = '';

if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'sesion_expirada') {
    $mensaje = 'Tu sesión ha expirado por inactividad. Por favor, ingresa de nuevo.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarTokenCSRF($_POST['csrf_token'] ?? '');

    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($correo) || empty($password)) {
        $error = 'Por favor ingresa tu correo y contraseña.';
    } else {
        // Consulta del usuario mediante consulta preparada (PDO)
        $stmt = $pdo->prepare('SELECT id, nombre, correo, password_hash FROM estudiantes WHERE correo = :correo');
        $stmt->execute([':correo' => $correo]);
        $estudiante = $stmt->fetch();

        // Verificar hash de contraseña
        if ($estudiante && password_verify($password, $estudiante['password_hash'])) {
            // Iniciar sesión y guardar datos mínimos
            $_SESSION['estudiante_id'] = $estudiante['id'];
            $_SESSION['estudiante_nombre'] = $estudiante['nombre'];
            $_SESSION['estudiante_correo'] = $estudiante['correo'];
            $_SESSION['ultimo_acceso'] = time();

            header('Location: dashboard.php');
            exit;
        } else {
            // Mensaje genérico por seguridad (sin revelar si el correo existe)
            $error = 'Credenciales incorrectas. Verifica tus datos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Iniciar Sesión - Portal de Asesorías</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Iniciar Sesión</h3></div>
                                <div class="card-body">
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                    <?php endif; ?>

                                    <?php if ($mensaje): ?>
                                        <div class="alert alert-warning"><?= htmlspecialchars($mensaje) ?></div>
                                    <?php endif; ?>

                                    <form method="POST" action="login.php">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(obtenerTokenCSRF()) ?>">

                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" name="correo" type="email" placeholder="nombre@ejemplo.com" required />
                                            <label for="inputEmail">Correo electrónico</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Contraseña" required />
                                            <label for="inputPassword">Contraseña</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="register.php">¿No tienes cuenta? ¡Regístrate aquí!</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>