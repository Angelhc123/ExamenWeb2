<?php
// PANEL ADMINISTRADOR SIMPLE
session_start();

if (!isset($_SESSION['logueado']) || $_SESSION['tipo'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Configuración BD
try {
    $pdo = new PDO("mysql:host=localhost;dbname=consejeria_tutoria", "root", "");
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

// OBTENER FILTROS
$filtro_semestre = $_GET['semestre'] ?? '';
$filtro_docente = $_GET['docente'] ?? '';
$filtro_categoria = $_GET['categoria'] ?? '';
$buscar = $_GET['buscar'] ?? '';

// CONSTRUIR CONSULTA
$sql = "SELECT a.*, 
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
    WHERE 1=1";

$params = [];

if ($filtro_semestre) {
    $sql .= " AND a.semestre_id = ?";
    $params[] = $filtro_semestre;
}
if ($filtro_docente) {
    $sql .= " AND a.docente_id = ?";
    $params[] = $filtro_docente;
}
if ($filtro_categoria) {
    $sql .= " AND tc.categoria = ?";
    $params[] = $filtro_categoria;
}
if ($buscar) {
    $sql .= " AND (e.nombres LIKE ? OR e.apellidos LIKE ? OR e.codigo LIKE ? OR a.consulta_estudiante LIKE ?)";
    $params[] = "%$buscar%";
    $params[] = "%$buscar%";
    $params[] = "%$buscar%";
    $params[] = "%$buscar%";
}

$sql .= " ORDER BY a.created_at DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$atenciones = $stmt->fetchAll();

// Datos para filtros
$semestres = $pdo->query("SELECT * FROM semestres ORDER BY nombre DESC")->fetchAll();
$docentes = $pdo->query("SELECT * FROM docentes ORDER BY apellidos")->fetchAll();

// Estadísticas básicas
$total_atenciones = count($atenciones);
$total_general = $pdo->query("SELECT COUNT(*) as total FROM atenciones")->fetch()['total'];

// Estadísticas por categoría
$stats_cat = $pdo->query("
    SELECT tc.categoria, COUNT(*) as total 
    FROM atenciones a 
    JOIN temas_consejeria tc ON a.tema_id = tc.id 
    GROUP BY tc.categoria 
    ORDER BY total DESC
")->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Consejería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- NAV -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">
                <i class="bi bi-shield-check"></i> Panel Admin - <?= $_SESSION['usuario'] ?>
            </span>
            <a href="?logout=1" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Salir
            </a>
        </div>
    </nav>

    <div class="container-fluid mt-3">
        
        <!-- ESTADÍSTICAS -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card text-bg-primary">
                    <div class="card-body text-center">
                        <h3><?= $total_general ?></h3>
                        <p class="mb-0">Total Atenciones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Atenciones por Categoría:</h6>
                        <?php foreach ($stats_cat as $cat): ?>
                            <span class="badge bg-info me-2 mb-1">
                                <?= ucwords(str_replace('_', ' ', $cat['categoria'])) ?>: <?= $cat['total'] ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTROS -->
        <div class="card mb-3">
            <div class="card-header">
                <h5><i class="bi bi-funnel"></i> Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-2">
                        <select name="semestre" class="form-select form-select-sm">
                            <option value="">Todos los semestres</option>
                            <?php foreach ($semestres as $sem): ?>
                                <option value="<?= $sem['id'] ?>" <?= $filtro_semestre == $sem['id'] ? 'selected' : '' ?>>
                                    <?= $sem['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <select name="docente" class="form-select form-select-sm">
                            <option value="">Todos los docentes</option>
                            <?php foreach ($docentes as $doc): ?>
                                <option value="<?= $doc['id'] ?>" <?= $filtro_docente == $doc['id'] ? 'selected' : '' ?>>
                                    <?= $doc['apellidos'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <select name="categoria" class="form-select form-select-sm">
                            <option value="">Todas las categorías</option>
                            <option value="plan_estudios" <?= $filtro_categoria == 'plan_estudios' ? 'selected' : '' ?>>Plan de Estudios</option>
                            <option value="desarrollo_profesional" <?= $filtro_categoria == 'desarrollo_profesional' ? 'selected' : '' ?>>Desarrollo Profesional</option>
                            <option value="insercion_laboral" <?= $filtro_categoria == 'insercion_laboral' ? 'selected' : '' ?>>Inserción Laboral</option>
                            <option value="plan_tesis" <?= $filtro_categoria == 'plan_tesis' ? 'selected' : '' ?>>Plan de Tesis</option>
                            <option value="otros" <?= $filtro_categoria == 'otros' ? 'selected' : '' ?>>Otros</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <input type="text" name="buscar" class="form-control form-control-sm" 
                               placeholder="Buscar estudiante, código..." value="<?= htmlspecialchars($buscar) ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                    </div>
                    
                    <div class="col-md-1">
                        <a href="?" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-x"></i> Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- RESULTADOS -->
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-list-ul"></i> Atenciones Registradas (<?= $total_atenciones ?> resultados)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($atenciones)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No se encontraron atenciones con los filtros seleccionados.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Estudiante</th>
                                    <th>Docente</th>
                                    <th>Categoría</th>
                                    <th>Tema</th>
                                    <th>Consulta</th>
                                    <th>Semestre</th>
                                    <th>Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($atenciones as $atencion): ?>
                                <tr>
                                    <td><small><?= date('d/m/Y', strtotime($atencion['fecha_atencion'])) ?></small></td>
                                    <td>
                                        <strong><?= $atencion['estudiante'] ?></strong><br>
                                        <small class="text-muted"><?= $atencion['codigo_estudiante'] ?></small>
                                    </td>
                                    <td><small><?= $atencion['docente'] ?></small></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $atencion['categoria'] == 'plan_estudios' ? 'primary' : 
                                            ($atencion['categoria'] == 'desarrollo_profesional' ? 'success' : 
                                            ($atencion['categoria'] == 'insercion_laboral' ? 'warning text-dark' : 
                                            ($atencion['categoria'] == 'plan_tesis' ? 'info' : 'secondary')))
                                        ?>">
                                            <?= ucwords(str_replace('_', ' ', $atencion['categoria'])) ?>
                                        </span>
                                    </td>
                                    <td><small><?= $atencion['tema'] ?></small></td>
                                    <td>
                                        <small class="text-muted">
                                            <?= substr($atencion['consulta_estudiante'], 0, 50) ?>...
                                        </small>
                                    </td>
                                    <td><small><?= $atencion['semestre'] ?></small></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="verDetalle(<?= htmlspecialchars(json_encode($atencion)) ?>)">
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

    <!-- MODAL -->
    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de Atención</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalleContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function verDetalle(atencion) {
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>📋 Información:</h6>
                    <p><strong>Estudiante:</strong> ${atencion.estudiante}<br>
                    <strong>Código:</strong> ${atencion.codigo_estudiante}<br>
                    <strong>Carrera:</strong> ${atencion.carrera || 'No especificada'}</p>
                    
                    <p><strong>Docente:</strong> ${atencion.docente}<br>
                    <strong>Fecha:</strong> ${atencion.fecha_atencion}<br>
                    <strong>Hora:</strong> ${atencion.hora_inicio}</p>
                </div>
                <div class="col-md-6">
                    <h6>🎯 Tema:</h6>
                    <p><span class="badge bg-primary">${atencion.categoria.replace(/_/g, ' ').toUpperCase()}</span><br>
                    <strong>${atencion.tema}</strong></p>
                    
                    <p><strong>Semestre:</strong> ${atencion.semestre}</p>
                </div>
            </div>
            
            <h6>💬 Consulta del Estudiante:</h6>
            <div class="bg-light p-3 rounded mb-3">
                ${atencion.consulta_estudiante}
            </div>
            
            ${atencion.descripcion_atencion ? 
                '<h6>✍️ Respuesta del Docente:</h6><div class="bg-info bg-opacity-10 p-3 rounded mb-3">' + atencion.descripcion_atencion + '</div>' : 
                '<div class="alert alert-warning"><i class="bi bi-clock"></i> Pendiente de respuesta del docente</div>'
            }
            
            ${atencion.observaciones ? 
                '<h6>📝 Observaciones:</h6><div class="bg-secondary bg-opacity-10 p-3 rounded">' + atencion.observaciones + '</div>' : ''
            }
        `;
        
        document.getElementById('detalleContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('modalDetalle')).show();
    }
    </script>
</body>
</html>