<?php
// PANEL DE ESTUDIANTE
session_start();

if (!isset($_SESSION['logueado']) || $_SESSION['tipo'] != 'estudiante') {
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

// PROCESAR NUEVA ATENCIÓN
if ($_POST) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO atenciones (estudiante_id, docente_id, semestre_id, tema_id, 
                                   fecha_atencion, hora_inicio, consulta_estudiante, 
                                   descripcion_atencion, observaciones) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_SESSION['usuario_id'],
            $_POST['docente_id'], 
            $_POST['semestre_id'],
            $_POST['tema_id'],
            $_POST['fecha_atencion'],
            $_POST['hora_inicio'],
            $_POST['consulta_estudiante'],
            $_POST['descripcion_atencion'] ?? '',
            $_POST['observaciones'] ?? ''
        ]);
        
        $success = "✅ Solicitud de atención registrada exitosamente!";
    } catch (Exception $e) {
        $error = "❌ Error: " . $e->getMessage();
    }
}

// Obtener datos para formulario
$docentes = $pdo->query("SELECT * FROM docentes WHERE activo = 1 ORDER BY apellidos")->fetchAll();
$semestres = $pdo->query("SELECT * FROM semestres ORDER BY activo DESC, id DESC")->fetchAll();
$temas = $pdo->query("SELECT * FROM temas_consejeria ORDER BY categoria, nombre")->fetchAll();

// Obtener MIS atenciones
$stmt = $pdo->prepare("
    SELECT a.*, 
           CONCAT(d.nombres, ' ', d.apellidos) as docente,
           s.nombre as semestre,
           tc.nombre as tema,
           tc.categoria
    FROM atenciones a 
    JOIN docentes d ON a.docente_id = d.id
    JOIN semestres s ON a.semestre_id = s.id
    JOIN temas_consejeria tc ON a.tema_id = tc.id
    WHERE a.estudiante_id = ?
    ORDER BY a.created_at DESC 
");
$stmt->execute([$_SESSION['usuario_id']]);
$mis_atenciones = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel Estudiante - Consejería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- NAVEGACIÓN -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-person-graduation"></i> Portal Estudiante
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    👨‍🎓 <?= $_SESSION['usuario'] ?> (<?= $_SESSION['codigo'] ?>)
                </span>
                <a href="?logout=1" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- FORMULARIO NUEVA ATENCIÓN -->
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5><i class="bi bi-plus-circle"></i> Solicitar Nueva Atención</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Docente/Tutor:</label>
                                <select name="docente_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($docentes as $doc): ?>
                                        <option value="<?= $doc['id'] ?>">
                                            <?= $doc['nombres'] ?> <?= $doc['apellidos'] ?>
                                            <?php if ($doc['especialidad']): ?>
                                                - <?= $doc['especialidad'] ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Semestre:</label>
                                        <select name="semestre_id" class="form-select" required>
                                            <?php foreach ($semestres as $sem): ?>
                                                <option value="<?= $sem['id'] ?>" <?= $sem['activo'] ? 'selected' : '' ?>>
                                                    <?= $sem['nombre'] ?> <?= $sem['activo'] ? '(Actual)' : '' ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha Preferida:</label>
                                        <input type="date" name="fecha_atencion" class="form-control" 
                                               value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Hora Preferida:</label>
                                <input type="time" name="hora_inicio" class="form-control" value="09:00" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tema de Consulta:</label>
                                <select name="tema_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php 
                                    $categoria_actual = '';
                                    foreach ($temas as $tema): 
                                        if ($categoria_actual != $tema['categoria']):
                                            $categoria_actual = $tema['categoria'];
                                            echo '<optgroup label="' . strtoupper(str_replace('_', ' ', $categoria_actual)) . '">';
                                        endif;
                                    ?>
                                        <option value="<?= $tema['id'] ?>">
                                            <?= $tema['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Describe tu Consulta:</label>
                                <textarea name="consulta_estudiante" class="form-control" rows="4" required 
                                          placeholder="Explica detalladamente tu consulta, problema o duda..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-send"></i> Enviar Solicitud
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- MIS ATENCIONES -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="bi bi-list-check"></i> Mis Atenciones (<?= count($mis_atenciones) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($mis_atenciones)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Aún no tienes atenciones registradas.
                            </div>
                        <?php else: ?>
                            <div style="max-height: 500px; overflow-y: auto;">
                                <?php foreach ($mis_atenciones as $atencion): ?>
                                <div class="card mb-2">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <strong><?= $atencion['docente'] ?></strong>
                                            <small class="text-muted">
                                                <?= date('d/m/Y', strtotime($atencion['fecha_atencion'])) ?>
                                            </small>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge bg-primary"><?= strtoupper(str_replace('_', ' ', $atencion['categoria'])) ?></span>
                                            <small class="text-muted ms-2"><?= $atencion['tema'] ?></small>
                                        </div>
                                        <div class="text-muted small">
                                            <strong>Tu consulta:</strong><br>
                                            <?= nl2br(htmlspecialchars(substr($atencion['consulta_estudiante'], 0, 100))) ?>
                                            <?= strlen($atencion['consulta_estudiante']) > 100 ? '...' : '' ?>
                                        </div>
                                        <?php if ($atencion['descripcion_atencion']): ?>
                                        <div class="mt-2 p-2 bg-light rounded">
                                            <small><strong>Respuesta del docente:</strong><br>
                                            <?= nl2br(htmlspecialchars($atencion['descripcion_atencion'])) ?></small>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>