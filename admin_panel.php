<?php
// PANEL ADMINISTRADOR
session_start();

if (!isset($_SESSION['logueado']) || $_SESSION['tipo'] != 'admin') {
    header('Location: login.php');
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

// FILTROS
$filtro_semestre = $_GET['semestre'] ?? '';
$filtro_docente = $_GET['docente'] ?? '';
$filtro_categoria = $_GET['categoria'] ?? '';
$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';

// CONSTRUIR QUERY CON FILTROS
$where = [];
$params = [];

if ($filtro_semestre) {
    $where[] = "a.semestre_id = ?";
    $params[] = $filtro_semestre;
}
if ($filtro_docente) {
    $where[] = "a.docente_id = ?";
    $params[] = $filtro_docente;
}
if ($filtro_categoria) {
    $where[] = "tc.categoria = ?";
    $params[] = $filtro_categoria;
}
if ($filtro_fecha_desde) {
    $where[] = "a.fecha_atencion >= ?";
    $params[] = $filtro_fecha_desde;
}
if ($filtro_fecha_hasta) {
    $where[] = "a.fecha_atencion <= ?";
    $params[] = $filtro_fecha_hasta;
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// OBTENER ATENCIONES CON FILTROS
$sql = "
    SELECT a.*, 
           CONCAT(e.nombres, ' ', e.apellidos) as estudiante,
           e.codigo as codigo_estudiante,
           e.carrera,
           CONCAT(d.nombres, ' ', d.apellidos) as docente,
           s.nombre as semestre,
           tc.nombre as tema,
           tc.categoria
    FROM atenciones a 
    JOIN estudiantes e ON a.estudiante_id = e.id
    JOIN docentes d ON a.docente_id = d.id
    JOIN semestres s ON a.semestre_id = s.id
    JOIN temas_consejeria tc ON a.tema_id = tc.id
    $where_sql
    ORDER BY a.created_at DESC 
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$atenciones = $stmt->fetchAll();

// OBTENER DATOS PARA FILTROS
$semestres = $pdo->query("SELECT * FROM semestres ORDER BY id DESC")->fetchAll();
$docentes = $pdo->query("SELECT * FROM docentes ORDER BY apellidos")->fetchAll();

// ESTADÍSTICAS RÁPIDAS
$total_atenciones = count($atenciones);

$stats_categoria = $pdo->prepare("
    SELECT tc.categoria, COUNT(*) as total 
    FROM atenciones a 
    JOIN temas_consejeria tc ON a.tema_id = tc.id 
    JOIN semestres s ON a.semestre_id = s.id
    JOIN docentes d ON a.docente_id = d.id
    $where_sql
    GROUP BY tc.categoria
    ORDER BY total DESC
");
$stats_categoria->execute($params);
$estadisticas_categoria = $stats_categoria->fetchAll();

$stats_semestre = $pdo->prepare("
    SELECT s.nombre, COUNT(*) as total 
    FROM atenciones a 
    JOIN semestres s ON a.semestre_id = s.id 
    JOIN temas_consejeria tc ON a.tema_id = tc.id
    JOIN docentes d ON a.docente_id = d.id
    $where_sql
    GROUP BY s.nombre 
    ORDER BY total DESC
");
$stats_semestre->execute($params);
$estadisticas_semestre = $stats_semestre->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel Administrador - Consejería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- NAVEGACIÓN -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-shield-check"></i> Panel Administrador
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    👨‍💼 <?= $_SESSION['usuario'] ?> (<?= $_SESSION['rol'] ?>)
                </span>
                <a href="?logout=1" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-3">
        <!-- ESTADÍSTICAS RÁPIDAS -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h4><?= $total_atenciones ?></h4>
                        <p class="mb-0">Total Atenciones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <h6>Por Categoría:</h6>
                        <?php foreach ($estadisticas_categoria as $stat): ?>
                            <span class="badge bg-info me-2">
                                <?= strtoupper(str_replace('_', ' ', $stat['categoria'])) ?>: <?= $stat['total'] ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTROS -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5><i class="bi bi-funnel"></i> Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Semestre:</label>
                        <select name="semestre" class="form-select">
                            <option value="">Todos</option>
                            <?php foreach ($semestres as $sem): ?>
                                <option value="<?= $sem['id'] ?>" <?= $filtro_semestre == $sem['id'] ? 'selected' : '' ?>>
                                    <?= $sem['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Docente:</label>
                        <select name="docente" class="form-select">
                            <option value="">Todos</option>
                            <?php foreach ($docentes as $doc): ?>
                                <option value="<?= $doc['id'] ?>" <?= $filtro_docente == $doc['id'] ? 'selected' : '' ?>>
                                    <?= $doc['apellidos'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Categoría:</label>
                        <select name="categoria" class="form-select">
                            <option value="">Todas</option>
                            <option value="plan_estudios" <?= $filtro_categoria == 'plan_estudios' ? 'selected' : '' ?>>Plan Estudios</option>
                            <option value="desarrollo_profesional" <?= $filtro_categoria == 'desarrollo_profesional' ? 'selected' : '' ?>>Desarrollo Prof.</option>
                            <option value="insercion_laboral" <?= $filtro_categoria == 'insercion_laboral' ? 'selected' : '' ?>>Inserción Laboral</option>
                            <option value="plan_tesis" <?= $filtro_categoria == 'plan_tesis' ? 'selected' : '' ?>>Plan Tesis</option>
                            <option value="otros" <?= $filtro_categoria == 'otros' ? 'selected' : '' ?>>Otros</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Desde:</label>
                        <input type="date" name="fecha_desde" class="form-control" value="<?= $filtro_fecha_desde ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Hasta:</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="<?= $filtro_fecha_hasta ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                    </div>
                </form>
                
                <?php if ($filtro_semestre || $filtro_docente || $filtro_categoria || $filtro_fecha_desde): ?>
                <div class="mt-2">
                    <a href="?" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i> Limpiar Filtros
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- TABLA DE ATENCIONES -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5><i class="bi bi-table"></i> Registro de Atenciones (<?= $total_atenciones ?> resultados)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($atenciones)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No se encontraron atenciones con los filtros aplicados.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Estudiante</th>
                                    <th>Docente</th>
                                    <th>Tema</th>
                                    <th>Consulta</th>
                                    <th>Semestre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($atenciones as $atencion): ?>
                                <tr>
                                    <td><?= $atencion['id'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($atencion['fecha_atencion'])) ?></td>
                                    <td>
                                        <strong><?= $atencion['estudiante'] ?></strong><br>
                                        <small class="text-muted"><?= $atencion['codigo_estudiante'] ?> - <?= $atencion['carrera'] ?></small>
                                    </td>
                                    <td><?= $atencion['docente'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $atencion['categoria'] == 'plan_estudios' ? 'primary' : 
                                            ($atencion['categoria'] == 'desarrollo_profesional' ? 'success' : 
                                            ($atencion['categoria'] == 'insercion_laboral' ? 'warning' : 
                                            ($atencion['categoria'] == 'plan_tesis' ? 'info' : 'secondary')))
                                        ?>">
                                            <?= strtoupper(str_replace('_', ' ', $atencion['categoria'])) ?>
                                        </span><br>
                                        <small><?= $atencion['tema'] ?></small>
                                    </td>
                                    <td>
                                        <small><?= substr($atencion['consulta_estudiante'], 0, 80) ?>...</small>
                                    </td>
                                    <td><?= $atencion['semestre'] ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="verDetalle(<?= $atencion['id'] ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- MODAL DETALLE -->
    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de Atención</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalleContent">
                    <!-- Se carga dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function verDetalle(id) {
        // Buscar la atención en la data actual
        const atenciones = <?= json_encode($atenciones) ?>;
        const atencion = atenciones.find(a => a.id == id);
        
        if (atencion) {
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información Básica:</h6>
                        <p><strong>Estudiante:</strong> ${atencion.estudiante}<br>
                        <strong>Código:</strong> ${atencion.codigo_estudiante}<br>
                        <strong>Carrera:</strong> ${atencion.carrera}</p>
                        
                        <p><strong>Docente:</strong> ${atencion.docente}<br>
                        <strong>Fecha:</strong> ${atencion.fecha_atencion}<br>
                        <strong>Hora:</strong> ${atencion.hora_inicio}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Tema:</h6>
                        <p><span class="badge bg-primary">${atencion.categoria.replace('_', ' ').toUpperCase()}</span><br>
                        ${atencion.tema}</p>
                        
                        <p><strong>Semestre:</strong> ${atencion.semestre}</p>
                    </div>
                </div>
                
                <h6>Consulta del Estudiante:</h6>
                <p class="bg-light p-3 rounded">${atencion.consulta_estudiante}</p>
                
                ${atencion.descripcion_atencion ? 
                    '<h6>Respuesta/Descripción:</h6><p class="bg-info bg-opacity-10 p-3 rounded">' + atencion.descripcion_atencion + '</p>' : 
                    '<div class="alert alert-warning">Sin respuesta del docente aún</div>'
                }
                
                ${atencion.observaciones ? 
                    '<h6>Observaciones:</h6><p>' + atencion.observaciones + '</p>' : ''
                }
            `;
            
            document.getElementById('detalleContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        }
    }
    </script>
</body>
</html>