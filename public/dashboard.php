<?php
// public/dashboard.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/database.php';

// Validar que el usuario esté autenticado[cite: 22, 23]
requerirAutenticacion();

// 1. Total de solicitudes del alumno autenticado[cite: 22]
$stmt = $pdo->prepare('SELECT COUNT(*) FROM solicitudes WHERE estudiante_id = :id');
$stmt->execute([':id' => $_SESSION['estudiante_id']]);
$total_solicitudes = $stmt->fetchColumn();

// 2. Consulta para obtener el conteo de solicitudes por día del alumno[cite: 22]
$stmtGrafica = $pdo->prepare("
    SELECT DATE_FORMAT(fecha_registro, '%Y-%m-%d') AS fecha, COUNT(*) AS total 
    FROM solicitudes 
    WHERE estudiante_id = :id 
    GROUP BY DATE(fecha_registro) 
    ORDER BY fecha_registro ASC 
    LIMIT 7
");
$stmtGrafica->execute([':id' => $_SESSION['estudiante_id']]);
$datosGrafica = $stmtGrafica->fetchAll();

$fechasJS = [];
$totalesJS = [];

foreach ($datosGrafica as $fila) {
    $fechasJS[] = $fila['fecha'];
    $totalesJS[] = (int)$fila['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Área Personal - Portal de Asesorías</title>
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
                        <a class="nav-link active" href="dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Inicio
                        </a>
                        <a class="nav-link" href="solicitar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-edit"></i></div>
                            Solicitar Asesoría
                        </a>
                        <a class="nav-link" href="mis-solicitudes.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Mis Solicitudes
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Conectado como:</div>
                    <?= htmlspecialchars($_SESSION['estudiante_correo']) ?>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Bienvenido(a), <?= htmlspecialchars($_SESSION['estudiante_nombre']) ?></h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Panel de Control General</li>
                    </ol>

                    <!-- Tarjetas Superiores -->
                    <div class="row">
                        <div class="col-xl-6 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <h4 class="card-title">Mis Solicitudes Registradas</h4>
                                    <p class="card-text fs-2"><?= $total_solicitudes ?></p>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="mis-solicitudes.php">Ver Historial</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <h4 class="card-title">¿Necesitas Ayuda Académica?</h4>
                                    <p class="card-text">Registra un nuevo tema o materia para asignarle un asesor.</p>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="solicitar.php">Nueva Solicitud</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfica de Puntos / Área -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-area me-1"></i>
                                    Solicitudes Realizadas por Día
                                </div>
                                <div class="card-body">
                                    <canvas id="myAreaChart" width="100%" height="30"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>

    <!-- Librería Chart.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

    <!-- Configuración e inicialización de la gráfica -->
    <script>
        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';

        var ctx = document.getElementById("myAreaChart");
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($fechasJS) ?>,
                datasets: [{
                    label: "Solicitudes",
                    lineTension: 0.3,
                    backgroundColor: "rgba(2,117,216,0.2)",
                    borderColor: "rgba(2,117,216,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(2,117,216,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: "rgba(2,117,216,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: <?= json_encode($totalesJS) ?>,
                }],
            },
            options: {
                scales: {
                    xAxes: [{
                        time: { unit: 'date' },
                        gridLines: { display: false },
                        ticks: { maxTicksLimit: 7 }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            stepSize: 1,
                            precision: 0
                        },
                        gridLines: { color: "rgba(0, 0, 0, .125)" }
                    }],
                },
                legend: { display: false }
            }
        });
    </script>
</body>
</html>