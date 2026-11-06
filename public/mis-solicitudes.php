<?php
// public/mis-solicitudes.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/database.php';

requerirAutenticacion();

// Consulta filtrada estricta por el ID del usuario en sesión
$stmt = $pdo->prepare('SELECT id, materia, descripcion, disponibilidad, estado, fecha_registro FROM solicitudes WHERE estudiante_id = :estudiante_id ORDER BY fecha_registro DESC');
$stmt->execute([':estudiante_id' => $_SESSION['estudiante_id']]);
$solicitudes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Mis Solicitudes - Portal de Asesorías</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
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
                        <a class="nav-link" href="solicitar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-edit"></i></div>
                            Solicitar Asesoría
                        </a>
                        <a class="nav-link active" href="mis-solicitudes.php">
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
                    <h1 class="mt-4">Mis Solicitudes de Asesoría</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Mis Solicitudes</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header"><i class="fas fa-table me-1"></i> Historial Personal de Solicitudes</div>
                        <div class="card-body">
                            <?php if (empty($solicitudes)): ?>
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-1"></i> Aún no has registrado ninguna solicitud de asesoría. <a href="solicitar.php" class="alert-link">Haz clic aquí para crear una.</a>
                                </div>
                            <?php else: ?>
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Materia / Tema</th>
                                            <th>Descripción</th>
                                            <th>Disponibilidad</th>
                                            <th>Estado</th>
                                            <th>Fecha de Registro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($solicitudes as $index => $sol): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($sol['materia']) ?></td>
                                                <td><?= htmlspecialchars($sol['descripcion']) ?></td>
                                                <td><?= htmlspecialchars($sol['disponibilidad']) ?></td>
                                                <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($sol['estado']) ?></span></td>
                                                <td><?= htmlspecialchars($sol['fecha_registro']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>