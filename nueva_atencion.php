<?php
// NUEVA ATENCIÓN - SIMPLE
session_start();

if (!isset($_SESSION['logueado'])) {
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

// PROCESAR FORMULARIO
if ($_POST) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO atenciones (estudiante_id, docente_id, semestre_id, tema_id, 
                                   fecha_atencion, hora_inicio, consulta_estudiante, 
                                   descripcion_atencion, observaciones) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_POST['estudiante_id'],
            $_POST['docente_id'], 
            $_POST['semestre_id'],
            $_POST['tema_id'],
            $_POST['fecha_atencion'],
            $_POST['hora_inicio'],
            $_POST['consulta_estudiante'],
            $_POST['descripcion_atencion'],
            $_POST['observaciones']
        ]);
        
        $success = "✅ Atención registrada exitosamente!";
    } catch (Exception $e) {
        $error = "❌ Error: " . $e->getMessage();
    }
}

// Obtener datos para formulario
$estudiantes = $pdo->query("SELECT * FROM estudiantes ORDER BY apellidos")->fetchAll();
$docentes = $pdo->query("SELECT * FROM docentes ORDER BY apellidos")->fetchAll();
$semestres = $pdo->query("SELECT * FROM semestres ORDER BY id DESC")->fetchAll();
$temas = $pdo->query("SELECT * FROM temas_consejeria ORDER BY categoria, nombre")->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Nueva Atención - Consejería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- NAVEGACIÓN -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-mortarboard"></i> Consejería y Tutoría
            </a>
            <div class="navbar-nav ms-auto">
                <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <a href="?logout=1" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <div class="container mt-4">
        <h2><i class="bi bi-plus-circle"></i> Registrar Nueva Atención</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?= $success ?>
                <a href="dashboard.php" class="btn btn-primary btn-sm ms-2">Ver Atenciones</a>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estudiante:</label>
                                <select name="estudiante_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($estudiantes as $est): ?>
                                        <option value="<?= $est['id'] ?>">
                                            <?= $est['codigo'] ?> - <?= $est['nombres'] ?> <?= $est['apellidos'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Docente:</label>
                                <select name="docente_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($docentes as $doc): ?>
                                        <option value="<?= $doc['id'] ?>">
                                            <?= $doc['nombres'] ?> <?= $doc['apellidos'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Semestre:</label>
                                <select name="semestre_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($semestres as $sem): ?>
                                        <option value="<?= $sem['id'] ?>">
                                            <?= $sem['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Fecha:</label>
                                <input type="date" name="fecha_atencion" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Hora Inicio:</label>
                                <input type="time" name="hora_inicio" class="form-control" value="<?= date('H:i') ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tema de Consejería:</label>
                        <select name="tema_id" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($temas as $tema): ?>
                                <option value="<?= $tema['id'] ?>">
                                    [<?= strtoupper(str_replace('_', ' ', $tema['categoria'])) ?>] <?= $tema['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Consulta del Estudiante:</label>
                        <textarea name="consulta_estudiante" class="form-control" rows="3" required 
                                  placeholder="Describe la consulta o problema planteado por el estudiante..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción de la Atención:</label>
                        <textarea name="descripcion_atencion" class="form-control" rows="3"
                                  placeholder="Describe la atención brindada, consejos dados, etc..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observaciones:</label>
                        <textarea name="observaciones" class="form-control" rows="2"
                                  placeholder="Observaciones adicionales, seguimiento requerido, etc..."></textarea>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-save"></i> Registrar Atención
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary btn-lg ms-2">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>