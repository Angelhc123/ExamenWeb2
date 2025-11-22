<?php
// DASHBOARD PRINCIPAL - SIMPLE
session_start();

if (!isset($_SESSION['logueado'])) {
    header('Location: login.php');
    exit;
}

// Redirigir al panel correspondiente
if ($_SESSION['tipo'] == 'estudiante') {
    header('Location: estudiante_panel.php');
    exit;
} elseif ($_SESSION['tipo'] == 'admin') {
    header('Location: admin_panel_v2.php');
    exit;
}

// Configuración BD
define('DB_HOST', 'localhost');
define('DB_NAME', 'consejeria_tutoria');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Error BD: " . $e->getMessage());
}

// LOGOUT
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Obtener atenciones
$stmt = $pdo->query("
    SELECT a.*, 
           CONCAT(e.nombres, ' ', e.apellidos) as estudiante,
           CONCAT(d.nombres, ' ', d.apellidos) as docente,
           s.nombre as semestre,
           tc.nombre as tema
    FROM atenciones a 
    LEFT JOIN estudiantes e ON a.estudiante_id = e.id
    LEFT JOIN docentes d ON a.docente_id = d.id  
    LEFT JOIN semestres s ON a.semestre_id = s.id
    LEFT JOIN temas_consejeria tc ON a.tema_id = tc.id
    ORDER BY a.created_at DESC 
    LIMIT 20
");
$atenciones = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Consejería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- NAVEGACIÓN -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-mortarboard"></i> Consejería y Tutoría
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    👤 <?= $_SESSION['usuario'] ?> (<?= $_SESSION['rol'] ?>)
                </span>
                <a href="nueva_atencion.php" class="btn btn-success btn-sm me-2">
                    <i class="bi bi-plus"></i> Nueva Atención
                </a>
                <a href="?logout=1" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2><i class="bi bi-list-check"></i> Registro de Atenciones</h2>
                
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5>📋 Últimas Atenciones Registradas</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($atenciones)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No hay atenciones registradas.
                                <a href="nueva_atencion.php" class="btn btn-primary btn-sm ms-2">Registrar Primera Atención</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Estudiante</th>
                                            <th>Docente</th>
                                            <th>Tema</th>
                                            <th>Semestre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($atenciones as $atencion): ?>
                                        <tr>
                                            <td><?= $atencion['id'] ?></td>
                                            <td><?= date('d/m/Y', strtotime($atencion['fecha_atencion'])) ?></td>
                                            <td><?= $atencion['estudiante'] ?? 'N/A' ?></td>
                                            <td><?= $atencion['docente'] ?? 'N/A' ?></td>
                                            <td><?= $atencion['tema'] ?? 'N/A' ?></td>
                                            <td><?= $atencion['semestre'] ?? 'N/A' ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>