<?php
// public/solicitar.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/database.php';

requerirAutenticacion();

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarTokenCSRF($_POST['csrf_token'] ?? '');

    $materia = trim($_POST['materia'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $disponibilidad = trim($_POST['disponibilidad'] ?? '');

    if (empty($materia) || empty($descripcion) || empty($disponibilidad)) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        // La identidad se obtiene estricta y únicamente de la sesión
        $stmt = $pdo->prepare('INSERT INTO solicitudes (estudiante_id, materia, descripcion, disponibilidad, estado) VALUES (:estudiante_id, :materia, :descripcion, :disponibilidad, "Pendiente")');
        
        $resultado = $stmt->execute([
            ':estudiante_id' => $_SESSION['estudiante_id'],
            ':materia'       => $materia,
            ':descripcion'   => $descripcion,
            ':disponibilidad'=> $disponibilidad
        ]);

        if ($resultado) {
            $exito = 'Solicitud enviada correctamente. El estado inicial es Pendiente.';
        } else {
            $error = 'Error al registrar la solicitud.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Nueva Solicitud - Portal de Asesorías</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="dashboard.php">Portal Asesorías</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><span class="dropdown-item-text"><strong><?= htmlspecialchars($_SESSION['estudiante_nombre']) ?></strong></span></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li>
                        <form method="POST" action="logout.php" class="px-3 py-1">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(obtenerTokenCSRF()) ?>">
                            <button type="submit" class="btn btn-danger btn-sm w-100">Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Menú Principal</div>
                        <a class="nav-link" href="dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Inicio
                        </a>
                        <a class="nav-link active" href="solicitar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-edit"></i></div>
                            Solicitar Asesoría
                        </a>
                        <a class="nav-link" href="mis-solicitudes.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Mis Solicitudes
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Solicitar Asesoría Académica</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Nueva Solicitud</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header"><i class="fas fa-paper-plane me-1"></i> Formulario de Solicitud</div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>

                            <?php if ($exito): ?>
                                <div class="alert alert-success"><?= htmlspecialchars($exito) ?></div>
                            <?php endif; ?>

                            <form method="POST" action="solicitar.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(obtenerTokenCSRF()) ?>">

                                <div class="mb-3">
                                    <label for="materia" class="form-label">Materia o Tema</label>
                                    <input type="text" class="form-control" id="materia" name="materia" placeholder="Ej. Cálculo Diferencial, Estructura de Datos..." required>
                                </div>

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción de la necesidad</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" placeholder="Explica brevemente los temas específicos en los que necesitas apoyo..." required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="disponibilidad" class="form-label">Disponibilidad propuesta</label>
                                    <input type="text" class="form-control" id="disponibilidad" name="disponibilidad" placeholder="Ej. Lunes y Miércoles de 4:00 PM a 6:00 PM" required>
                                </div>

                                <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                            </form>
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