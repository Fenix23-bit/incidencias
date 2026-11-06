<?php
// public/register.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

$errores = [];
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validar Token CSRF
    verificarTokenCSRF($_POST['csrf_token'] ?? '');

    // 2. Limpiar y capturar entradas
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $rol = trim($_POST['rol'] ??'');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // 3. Validaciones en PHP
    if (empty($nombre) || empty($correo) || empty($password)) {
        $errores[] = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido.';
    } elseif ($password !== $password_confirm) {
        $errores[] = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        // 4. Verificar correo único en la base de datos
        $stmt = $pdo->prepare('SELECT id FROM usuario WHERE correo = :correo');
        $stmt->execute([':correo' => $correo]);

        if ($stmt->fetch()) {
            $errores[] = 'El correo ya se encuentra registrado.';
        } else {
            // 5. Guardar contraseña mediante Hash Seguro
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmtInsert = $pdo->prepare('INSERT INTO usuario (nombre, rol, correo, password_hash) VALUES (:nombre, :rol, :correo, :hash)');
            $resultado = $stmtInsert->execute([
                ':nombre' => $nombre,
                ':rol' => $rol,
                ':correo' => $correo,
                ':hash'   => $password_hash
            ]);

            if ($resultado) {
                $exito = 'Cuenta creada con éxito. Ya puedes iniciar sesión.';
            } else {
                $errores[] = 'Ocurrió un error al registrar la cuenta.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Registro - Portal de Asesorías</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Crear Cuenta de Estudiante</h3></div>
                                <div class="card-body">
                                    <?php if (!empty($errores)): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errores as $error): ?>
                                                    <li><?= htmlspecialchars($error) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($exito): ?>
                                        <div class="alert alert-success"><?= htmlspecialchars($exito) ?></div>
                                    <?php endif; ?>

                                    <form method="POST" action="register.php">
                                        <!-- Token CSRF oculto -->
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(obtenerTokenCSRF()) ?>">

                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputNombre" name="nombre" type="text" placeholder="Tu Nombre Completo" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" />
                                            <label for="inputNombre">Nombre completo</label>
                                        </div>

                                        <div class="form-floating mb-3">
    <select class="form-select" id="inputRol" name="rol" required>
        <option value="" disabled <?= !isset($_POST['rol']) ? 'selected' : '' ?>>Selecciona un rol</option>
        <option value="a" <?= (isset($_POST['rol']) && $_POST['rol'] === 'Reportante') ? 'selected' : '' ?>>Reportante</option>
        <option value="b" <?= (isset($_POST['rol']) && $_POST['rol'] === 'Administrador') ? 'selected' : '' ?>>Administrador</option>
    </select>
    <label for="inputRol">Rol</label>
</div>

                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputCorreo" name="correo" type="email" placeholder="nombre@ejemplo.com" required value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" />
                                            <label for="inputCorreo">Correo electrónico</label>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Contraseña" required />
                                                    <label for="inputPassword">Contraseña</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="inputPasswordConfirm" name="password_confirm" type="password" placeholder="Confirmar Contraseña" required />
                                                    <label for="inputPasswordConfirm">Confirmar Contraseña</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 mb-0">
                                            <div class="d-grid"><button type="submit" class="btn btn-primary btn-block">Crear Cuenta</button></div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="login.php">¿Ya tienes una cuenta? Inicia sesión</a></div>
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